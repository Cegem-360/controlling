<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Jobs\AnalyticsImport;
use App\Jobs\GoogleAdsImport;
use App\Jobs\SearchConsoleImport;
use App\Mail\GoogleAdsReportMail;
use App\Models\GoogleAdsSettings;
use App\Models\Settings as SettingsModel;
use App\Services\GoogleAds\GoogleAdsPdfGenerator;
use App\Services\GoogleAds\GoogleAdsReportService;
use App\Services\GoogleAdsOAuthService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Throwable;
use UnitEnum;

/**
 * @property-read Schema $form
 * @property-read Schema $googleAdsForm
 * @property-read Schema $emailSettingsForm
 */
final class Settings extends Page
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    /**
     * @var array<string, mixed> | null
     */
    public ?array $googleAdsData = [];

    /**
     * @var array<string, mixed> | null
     */
    public ?array $emailSettingsData = [];

    protected string $view = 'filament.pages.settings';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Configuration;

    protected static ?int $navigationSort = 99;

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray() ?? []);

        $googleAdsSettings = $this->getGoogleAdsSettings();
        $this->googleAdsForm->fill([
            'customer_id' => $googleAdsSettings?->customer_id,
            'manager_customer_id' => $googleAdsSettings?->manager_customer_id,
        ]);

        $this->emailSettingsForm->fill([
            'email_enabled' => $googleAdsSettings?->email_enabled ?? false,
            'email_recipients' => $googleAdsSettings?->email_recipients ?? [],
            'email_frequency' => $googleAdsSettings?->email_frequency ?? 'monthly',
            'email_day_of_week' => $googleAdsSettings?->email_day_of_week ?? 1,
            'email_day_of_month' => $googleAdsSettings?->email_day_of_month ?? 1,
        ]);
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

    public function googleAdsForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_id')
                    ->label('Customer ID')
                    ->placeholder('123-456-7890')
                    ->helperText(__('Your Google Ads Customer ID (found in the top right corner of Google Ads)'))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state): void {
                        $this->saveGoogleAdsField('customer_id', $state);
                    }),
                TextInput::make('manager_customer_id')
                    ->label('Manager Customer ID (MCC)')
                    ->placeholder('123-456-7890')
                    ->helperText(__('Only required if accessing through an MCC (Manager) account. Leave empty for direct access.'))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state): void {
                        $this->saveGoogleAdsField('manager_customer_id', $state);
                    }),
            ])
            ->statePath('googleAdsData');
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

        $oauthService = resolve(GoogleAdsOAuthService::class);
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

        $oauthService = resolve(GoogleAdsOAuthService::class);
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

        if (! $googleAdsSettings instanceof GoogleAdsSettings) {
            return;
        }

        $googleAdsSettings->update([$field => $value]);

        Notification::make()
            ->title(__('Settings saved'))
            ->success()
            ->send();
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
        $team = Filament::getTenant();

        if (! $googleAdsSettings instanceof GoogleAdsSettings || ! $team) {
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
            $reportData = $reportService->generateReportData($team, $reportMonth);
            $filename = $pdfGenerator->generate($reportData);
            $pdfPath = 'pdfs/' . $filename;

            Mail::to($recipients)
                ->send(new GoogleAdsReportMail(
                    team: $team,
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

    /**
     * @return array<int, Component>
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
