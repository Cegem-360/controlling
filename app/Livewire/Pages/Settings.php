<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Jobs\AnalyticsImport;
use App\Jobs\GoogleAdsImport;
use App\Jobs\SearchConsoleImport;
use App\Models\GoogleAdsSettings;
use App\Models\Settings as SettingsModel;
use App\Models\Team;
use App\Services\GoogleAdsOAuthService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
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
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class Settings extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?Team $team = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();

        if ($this->team) {
            $this->form->fill($this->getRecord()?->attributesToArray() ?? []);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make(__('Google Analytics Configuration'))
                        ->description(__('Configure Google Analytics 4 property settings for this team'))
                        ->schema([
                            TextInput::make('property_id')
                                ->label(__('GA4 Property ID'))
                                ->required()
                                ->maxLength(100)
                                ->placeholder('123456789'),
                            TextInput::make('google_tag_id')
                                ->label(__('Google Tag ID'))
                                ->required()
                                ->maxLength(100)
                                ->placeholder('G-XXXXXXXXXX'),
                        ])
                        ->columns(2),

                    Section::make(__('Google Search Console Configuration'))
                        ->description(__('Configure Search Console property settings for this team'))
                        ->schema([
                            TextInput::make('site_url')
                                ->label(__('Site URL'))
                                ->required()
                                ->url()
                                ->maxLength(500)
                                ->placeholder('https://example.com'),
                        ]),

                    Section::make(__('Google Ads Configuration'))
                        ->description(__('Configure Google Ads OAuth connection for this team'))
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
            $record->team_id = $this->team?->id;
        }

        $record->fill($data);
        $record->save();

        if ($record->wasRecentlyCreated) {
            $this->form->record($record)->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title(__('Settings saved'))
            ->send();
    }

    public function getRecord(): ?SettingsModel
    {
        return $this->team?->settings;
    }

    public function getGoogleAdsSettings(): ?GoogleAdsSettings
    {
        return $this->team?->googleAdsSettings;
    }

    public function connectGoogleAds(): void
    {
        if (! $this->team) {
            Notification::make()
                ->title(__('No team selected.'))
                ->danger()
                ->send();

            return;
        }

        $oauthService = app(GoogleAdsOAuthService::class);
        $authUrl = $oauthService->getAuthorizationUrl($this->team);

        $this->redirect($authUrl);
    }

    public function disconnectGoogleAds(): void
    {
        if (! $this->team) {
            Notification::make()
                ->title(__('No team selected.'))
                ->danger()
                ->send();

            return;
        }

        $oauthService = app(GoogleAdsOAuthService::class);
        $oauthService->disconnect($this->team);

        Notification::make()
            ->title(__('Google Ads disconnected successfully.'))
            ->success()
            ->send();

        $this->redirect(route('settings'));
    }

    public function performGoogleAdsSync(): void
    {
        if (! $this->team) {
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

        dispatch(new GoogleAdsImport($this->team->id));

        Notification::make()
            ->title(__('Google Ads sync started successfully.'))
            ->body(__('The Google Ads synchronization process has been initiated in the background.'))
            ->success()
            ->send();
    }

    public function performAnalyticsSync(): void
    {
        if (! $this->team) {
            Notification::make()
                ->title(__('No team selected.'))
                ->danger()
                ->send();

            return;
        }

        dispatch(new AnalyticsImport($this->team->id));

        Notification::make()
            ->title(__('Analytics sync started successfully.'))
            ->body(__('The Analytics synchronization process has been initiated in the background.'))
            ->success()
            ->send();
    }

    public function performSearchConsoleSync(): void
    {
        if (! $this->team) {
            Notification::make()
                ->title(__('No team selected.'))
                ->danger()
                ->send();

            return;
        }

        dispatch(new SearchConsoleImport($this->team->id));

        Notification::make()
            ->title(__('Search Console sync started successfully.'))
            ->body(__('The Search Console synchronization process has been initiated in the background.'))
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

    public function render(): ViewContract
    {
        return view('livewire.pages.settings');
    }

    public function syncAnalyticsAction(): Action
    {
        return Action::make('syncAnalytics')
            ->label(__('Sync Analytics'))
            ->icon('heroicon-o-chart-bar')
            ->color('primary')
            ->action('performAnalyticsSync');
    }

    public function syncSearchConsoleAction(): Action
    {
        return Action::make('syncSearchConsole')
            ->label(__('Sync Search Console'))
            ->icon('heroicon-o-magnifying-glass')
            ->color('success')
            ->action('performSearchConsoleSync');
    }

    public function syncGoogleAdsAction(): Action
    {
        return Action::make('syncGoogleAds')
            ->label(__('Sync Google Ads'))
            ->icon('heroicon-o-currency-dollar')
            ->color('warning')
            ->action('performGoogleAdsSync');
    }

    public function connectGoogleAdsAction(): Action
    {
        return Action::make('connectGoogleAds')
            ->label(__('Connect Google Ads'))
            ->icon('heroicon-o-link')
            ->color('warning')
            ->action('connectGoogleAds');
    }

    public function disconnectGoogleAdsAction(): Action
    {
        return Action::make('disconnectGoogleAds')
            ->label(__('Disconnect Google Ads'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__('Disconnect Google Ads'))
            ->modalDescription(__('Are you sure you want to disconnect your Google Ads account? This will remove the OAuth connection but keep your synced data.'))
            ->action('disconnectGoogleAds');
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
            Text::make(__('Click "Connect Google Ads" button to authenticate with your Google Ads account.'))
                ->color('gray'),
        ];
    }
}
