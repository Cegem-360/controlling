<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Jobs\AnalyticsImport;
use App\Jobs\GoogleAdsImport;
use App\Jobs\SearchConsoleImport;
use App\Models\GoogleAdsSettings;
use App\Models\Settings as SettingsModel;
use App\Services\GoogleAdsOAuthService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * @property-read Schema $form
 */
final class Settings extends Page
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected string $view = 'filament.pages.settings';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Configuration;

    protected static ?int $navigationSort = 99;

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray() ?? []);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Google Analytics Configuration')
                        ->description('Configure Google Analytics 4 property settings for this team')
                        ->schema([
                            TextInput::make('property_id')
                                ->label('GA4 Property ID')
                                ->required()
                                ->maxLength(100)
                                ->placeholder('123456789'),
                            TextInput::make('google_tag_id')
                                ->label('Google Tag ID')
                                ->required()
                                ->maxLength(100)
                                ->placeholder('G-XXXXXXXXXX'),
                        ])
                        ->columns(2),

                    Section::make('Google Search Console Configuration')
                        ->description('Configure Search Console property settings for this team')
                        ->schema([
                            TextInput::make('site_url')
                                ->label('Site URL')
                                ->required()
                                ->url()
                                ->maxLength(500)
                                ->placeholder('https://example.com'),
                        ]),

                    Section::make('Google Ads Configuration')
                        ->description('Configure Google Ads OAuth connection for this team')
                        ->schema($this->getGoogleAdsSchema()),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = $this->getRecord();

        if (! $record instanceof SettingsModel) {
            $record = new SettingsModel();
            $record->team_id = Filament::getTenant()?->id;
        }

        $record->fill($data);
        $record->save();

        if ($record->wasRecentlyCreated) {
            $this->form->record($record)->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->send();
    }

    public function getRecord(): ?SettingsModel
    {
        return Filament::getTenant()?->settings;
    }

    public function getGoogleAdsSettings(): ?GoogleAdsSettings
    {
        return Filament::getTenant()?->googleAdsSettings;
    }

    public function connectGoogleAds(): void
    {
        $team = Filament::getTenant();

        if (! $team) {
            Notification::make()
                ->title(__('No team selected.'))
                ->danger()
                ->send();

            return;
        }

        $oauthService = app(GoogleAdsOAuthService::class);
        $authUrl = $oauthService->getAuthorizationUrl($team);

        $this->redirect($authUrl);
    }

    public function disconnectGoogleAds(): void
    {
        $team = Filament::getTenant();

        if (! $team) {
            Notification::make()
                ->title(__('No team selected.'))
                ->danger()
                ->send();

            return;
        }

        $oauthService = app(GoogleAdsOAuthService::class);
        $oauthService->disconnect($team);

        Notification::make()
            ->title(__('Google Ads disconnected successfully.'))
            ->success()
            ->send();

        $this->redirect(self::getUrl());
    }

    public function performGoogleAdsSync(): void
    {
        $teamId = Filament::getTenant()?->id;

        if (! $teamId) {
            Notification::make()
                ->title(__('No team selected.'))
                ->danger()
                ->send();

            return;
        }

        $googleAdsSettings = $this->getGoogleAdsSettings();

        if (! $googleAdsSettings?->hasValidCredentials()) {
            Notification::make()
                ->title(__('Google Ads not connected.'))
                ->body(__('Please connect your Google Ads account first.'))
                ->danger()
                ->send();

            return;
        }

        dispatch(new GoogleAdsImport($teamId));

        Notification::make()
            ->title(__('Google Ads sync started successfully.'))
            ->body(__('The Google Ads synchronization process has been initiated in the background.'))
            ->success()
            ->send();
    }

    public function performAnalyticsSync(): void
    {
        $teamId = Filament::getTenant()?->id;

        if (! $teamId) {
            Notification::make()
                ->title('No team selected.')
                ->danger()
                ->send();

            return;
        }

        dispatch(new AnalyticsImport($teamId));

        Notification::make()
            ->title('Analytics sync started successfully.')
            ->body('The Analytics synchronization process has been initiated in the background.')
            ->success()
            ->send();
    }

    public function performSearchConsoleSync(): void
    {
        $teamId = Filament::getTenant()?->id;

        if (! $teamId) {
            Notification::make()
                ->title('No team selected.')
                ->danger()
                ->send();

            return;
        }

        dispatch(new SearchConsoleImport($teamId));

        Notification::make()
            ->title('Search Console sync started successfully.')
            ->body('The Search Console synchronization process has been initiated in the background.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();
        $isGoogleAdsConnected = $googleAdsSettings?->is_connected ?? false;

        $actions = [
            Action::make('syncAnalytics')
                ->label(__('Sync Analytics'))
                ->icon('heroicon-o-chart-bar')
                ->color('primary')
                ->action('performAnalyticsSync'),
            Action::make('syncSearchConsole')
                ->label(__('Sync Search Console'))
                ->icon('heroicon-o-magnifying-glass')
                ->color('success')
                ->action('performSearchConsoleSync'),
        ];

        if ($isGoogleAdsConnected) {
            $actions[] = Action::make('syncGoogleAds')
                ->label(__('Sync Google Ads'))
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->action('performGoogleAdsSync');

            $actions[] = Action::make('disconnectGoogleAds')
                ->label(__('Disconnect Google Ads'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Disconnect Google Ads'))
                ->modalDescription(__('Are you sure you want to disconnect your Google Ads account? This will remove the OAuth connection but keep your synced data.'))
                ->action('disconnectGoogleAds');
        } else {
            $actions[] = Action::make('connectGoogleAds')
                ->label(__('Connect Google Ads'))
                ->icon('heroicon-o-link')
                ->color('warning')
                ->action('connectGoogleAds');
        }

        return $actions;
    }

    /**
     * @return array<int, Component>
     */
    private function getGoogleAdsSchema(): array
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();
        $isConnected = $googleAdsSettings?->is_connected ?? false;

        if ($isConnected) {
            return [
                Placeholder::make('google_ads_status')
                    ->label('Connection Status')
                    ->content(new HtmlString(
                        '<span class="inline-flex items-center gap-1.5 rounded-full bg-success-500/10 px-3 py-1 text-sm font-medium text-success-600 dark:text-success-400">' .
                        '<span class="h-2 w-2 rounded-full bg-success-500"></span>' .
                        __('Connected') .
                        '</span>',
                    )),
                TextInput::make('google_ads_customer_id')
                    ->label('Customer ID')
                    ->default($googleAdsSettings?->customer_id)
                    ->disabled()
                    ->dehydrated(false),
                Placeholder::make('google_ads_last_sync')
                    ->label('Last Sync')
                    ->content($googleAdsSettings?->last_sync_at?->diffForHumans() ?? __('Never')),
            ];
        }

        return [
            Placeholder::make('google_ads_status')
                ->label('Connection Status')
                ->content(new HtmlString(
                    '<span class="inline-flex items-center gap-1.5 rounded-full bg-danger-500/10 px-3 py-1 text-sm font-medium text-danger-600 dark:text-danger-400">' .
                    '<span class="h-2 w-2 rounded-full bg-danger-500"></span>' .
                    __('Not Connected') .
                    '</span>',
                )),
            Placeholder::make('google_ads_connect_info')
                ->label('')
                ->content(__('Click "Connect Google Ads" in the header to authenticate with your Google Ads account.')),
        ];
    }
}
