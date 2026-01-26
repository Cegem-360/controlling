<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Jobs\AnalyticsImport;
use App\Jobs\GoogleAdsImport;
use App\Jobs\SearchConsoleImport;
use App\Mail\GoogleAdsReportMail;
use App\Models\GoogleAdsSettings;
use App\Models\Settings as SettingsModel;
use App\Models\Team;
use App\Services\GoogleAds\GoogleAdsPdfGenerator;
use App\Services\GoogleAds\GoogleAdsReportService;
use App\Services\GoogleAdsOAuthService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('components.layouts.dashboard')]
final class Settings extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?Team $team = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<string, mixed>|null */
    public ?array $emailSettingsData = [];

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();

        if ($this->team instanceof Team) {
            $this->form->fill($this->getRecord()?->attributesToArray() ?? []);

            $googleAdsSettings = $this->getGoogleAdsSettings();
            $this->emailSettingsForm->fill([
                'email_enabled' => $googleAdsSettings?->email_enabled ?? false,
                'email_recipients' => $googleAdsSettings?->email_recipients ?? [],
                'email_frequency' => $googleAdsSettings?->email_frequency ?? 'monthly',
                'email_day_of_week' => $googleAdsSettings?->email_day_of_week ?? 1,
                'email_day_of_month' => $googleAdsSettings?->email_day_of_month ?? 1,
            ]);
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

                    Section::make(__('Google Ads Email Reports'))
                        ->description(__('Configure automatic email delivery of Google Ads reports'))
                        ->schema($this->getEmailSettingsSchema()),
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
        if (! $this->ensureTeamSelected()) {
            return;
        }

        $oauthService = resolve(GoogleAdsOAuthService::class);
        $authUrl = $oauthService->getAuthorizationUrl($this->team);

        $this->redirect($authUrl);
    }

    public function disconnectGoogleAds(): void
    {
        if (! $this->ensureTeamSelected()) {
            return;
        }

        $oauthService = resolve(GoogleAdsOAuthService::class);
        $oauthService->disconnect($this->team);

        Notification::make()
            ->title(__('Google Ads disconnected successfully.'))
            ->success()
            ->send();

        $this->redirect(route('settings'));
    }

    public function performGoogleAdsSync(): void
    {
        if (! $this->ensureTeamSelected()) {
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

        $this->notifySyncStarted(__('Google Ads'));
    }

    public function performAnalyticsSync(): void
    {
        if (! $this->ensureTeamSelected()) {
            return;
        }

        dispatch(new AnalyticsImport($this->team->id));

        $this->notifySyncStarted(__('Analytics'));
    }

    public function performSearchConsoleSync(): void
    {
        if (! $this->ensureTeamSelected()) {
            return;
        }

        dispatch(new SearchConsoleImport($this->team->id));

        $this->notifySyncStarted(__('Search Console'));
    }

    public function saveGoogleAdsField(string $field, ?string $value): void
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();

        if (! $googleAdsSettings instanceof GoogleAdsSettings) {
            return;
        }

        $googleAdsSettings->update([$field => $value]);

        Notification::make()
            ->title(__('Settings saved'))
            ->success()
            ->send();
    }

    public function emailSettingsForm(Schema $schema): Schema
    {
        $isEmailEnabled = fn (Get $get): bool => $get('email_enabled') === true;

        return $schema
            ->components([
                Toggle::make('email_enabled')
                    ->label(__('Enable automatic email reports'))
                    ->helperText(__('When enabled, reports will be sent automatically according to the schedule below.'))
                    ->live(),
                TagsInput::make('email_recipients')
                    ->label(__('Email recipients'))
                    ->placeholder(__('Add email address...'))
                    ->helperText(__('Press Enter after each email address.'))
                    ->visible($isEmailEnabled),
                Select::make('email_frequency')
                    ->label(__('Frequency'))
                    ->options([
                        'weekly' => __('Weekly'),
                        'monthly' => __('Monthly'),
                    ])
                    ->default('monthly')
                    ->live()
                    ->visible($isEmailEnabled),
                Select::make('email_day_of_week')
                    ->label(__('Day of week'))
                    ->options([
                        1 => __('Monday'),
                        2 => __('Tuesday'),
                        3 => __('Wednesday'),
                        4 => __('Thursday'),
                        5 => __('Friday'),
                        6 => __('Saturday'),
                        7 => __('Sunday'),
                    ])
                    ->default(1)
                    ->visible(fn (Get $get): bool => $isEmailEnabled($get) && $get('email_frequency') === 'weekly'),
                Select::make('email_day_of_month')
                    ->label(__('Day of month'))
                    ->options(array_combine(range(1, 28), range(1, 28)))
                    ->default(1)
                    ->visible(fn (Get $get): bool => $isEmailEnabled($get) && $get('email_frequency') === 'monthly'),
            ])
            ->statePath('emailSettingsData');
    }

    public function saveEmailSettings(): void
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();

        if (! $googleAdsSettings instanceof GoogleAdsSettings) {
            Notification::make()
                ->title(__('Please connect Google Ads first.'))
                ->danger()
                ->send();

            return;
        }

        $data = $this->emailSettingsForm->getState();

        $googleAdsSettings->update([
            'email_enabled' => $data['email_enabled'] ?? false,
            'email_recipients' => $data['email_recipients'] ?? [],
            'email_frequency' => $data['email_frequency'] ?? 'monthly',
            'email_day_of_week' => $data['email_day_of_week'] ?? 1,
            'email_day_of_month' => $data['email_day_of_month'] ?? 1,
        ]);

        Notification::make()
            ->title(__('Email settings saved'))
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();

        if (! $googleAdsSettings instanceof GoogleAdsSettings || ! $this->team instanceof Team) {
            Notification::make()
                ->title(__('Please connect Google Ads first.'))
                ->danger()
                ->send();

            return;
        }

        $recipients = $googleAdsSettings->getEmailRecipients();

        if ($recipients === []) {
            Notification::make()
                ->title(__('No email recipients configured.'))
                ->body(__('Please add at least one email recipient and save the settings.'))
                ->danger()
                ->send();

            return;
        }

        try {
            $reportService = resolve(GoogleAdsReportService::class);
            $pdfGenerator = resolve(GoogleAdsPdfGenerator::class);

            $reportMonth = now()->subMonth();
            $reportData = $reportService->generateReportData($this->team, $reportMonth);
            $filename = $pdfGenerator->generate($reportData);
            $pdfPath = 'pdfs/' . $filename;

            Mail::to($recipients)
                ->send(new GoogleAdsReportMail(
                    team: $this->team,
                    pdfPath: $pdfPath,
                    reportMonth: $reportMonth,
                ));

            Notification::make()
                ->title(__('Test email sent successfully'))
                ->body(__('The test email has been sent to: ') . implode(', ', $recipients))
                ->success()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title(__('Failed to send test email'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
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

    private function ensureTeamSelected(): bool
    {
        if ($this->team instanceof Team) {
            return true;
        }

        Notification::make()
            ->title(__('No team selected.'))
            ->danger()
            ->send();

        return false;
    }

    private function notifySyncStarted(string $service): void
    {
        Notification::make()
            ->title(__(':service sync started successfully.', ['service' => $service]))
            ->body(__('The :service synchronization process has been initiated in the background.', ['service' => $service]))
            ->success()
            ->send();
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

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    private function getEmailSettingsSchema(): array
    {
        $googleAdsSettings = $this->getGoogleAdsSettings();
        $isConnected = $googleAdsSettings?->is_connected ?? false;

        if (! $isConnected) {
            return [
                Text::make(__('Please connect your Google Ads account first to configure email reports.'))
                    ->color('gray'),
            ];
        }

        return [
            View::make('filament.schemas.components.email-settings-form'),
            Actions::make([
                Action::make('saveEmailSettings')
                    ->label(__('Save email settings'))
                    ->action('saveEmailSettings'),
                Action::make('sendTestEmail')
                    ->label(__('Send test email'))
                    ->color('gray')
                    ->action('sendTestEmail'),
            ]),
        ];
    }
}
