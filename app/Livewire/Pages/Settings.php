<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Jobs\AnalyticsImport;
use App\Jobs\GoogleAdsImport;
use App\Jobs\SearchConsoleImport;
use App\Models\GoogleAdsSettings;
use App\Models\Settings as SettingsModel;
use App\Services\GoogleAdsOAuthService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\View;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('filament.layouts.app')]
final class Settings extends Component implements HasSchemas
{
    use InteractsWithSchemas;

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

                    /*  Section::make('Google Ads Configuration')
                        ->description('Configure Google Ads OAuth connection for this team')
                        ->schema($this->getGoogleAdsSchema()), */
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

    public function saveGoogleAdsField(string $field, ?string $value): void
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();

        if (! $googleAdsSettings) {
            return;
        }

        $googleAdsSettings->update([$field => $value]);

        Notification::make()
            ->title(__('Settings saved'))
            ->success()
            ->send();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.pages.settings');
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
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    private function getGoogleAdsSchema(): array
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();
        $isConnected = $googleAdsSettings?->is_connected ?? false;

        if ($isConnected) {
            return [
                Text::make(new HtmlString(
                    '<div class="flex items-center gap-2"><span class="text-sm font-medium text-gray-500 dark:text-gray-400">' . __('Connection Status') . ':</span>' .
                    '<span class="inline-flex items-center gap-1.5 rounded-full bg-success-500/10 px-3 py-1 text-sm font-medium text-success-600 dark:text-success-400">' .
                    '<span class="h-2 w-2 rounded-full bg-success-500"></span>' .
                    __('Connected') .
                    '</span></div>',
                )),
                View::make('filament.schemas.components.google-ads-form'),
                Text::make(new HtmlString(
                    '<div class="flex items-center gap-2"><span class="text-sm font-medium text-gray-500 dark:text-gray-400">' . __('Last Sync') . ':</span>' .
                    '<span class="text-sm text-gray-900 dark:text-white">' . ($googleAdsSettings?->last_sync_at?->diffForHumans() ?? __('Never')) . '</span></div>',
                )),
            ];
        }

        return [
            Text::make(new HtmlString(
                '<div class="flex items-center gap-2"><span class="text-sm font-medium text-gray-500 dark:text-gray-400">' . __('Connection Status') . ':</span>' .
                '<span class="inline-flex items-center gap-1.5 rounded-full bg-danger-500/10 px-3 py-1 text-sm font-medium text-danger-600 dark:text-danger-400">' .
                '<span class="h-2 w-2 rounded-full bg-danger-500"></span>' .
                __('Not Connected') .
                '</span></div>',
            )),
            Text::make(__('Click "Connect Google Ads" in the header to authenticate with your Google Ads account.'))
                ->color('gray'),
        ];
    }
}
