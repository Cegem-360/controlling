<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Jobs\SendGoogleAdsReportNowJob;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

final class SendGoogleAdsReportNowAction
{
    /**
     * Create the send report now action.
     *
     * @param  Team|null  $team  When provided, uses this team. Otherwise falls back to Filament tenant.
     */
    public static function make(?Team $team = null): Action
    {
        $resolvedTeam = $team ?? Filament::getTenant();
        $googleAdsSettings = $resolvedTeam?->googleAdsSettings;
        $defaultRecipients = $googleAdsSettings?->email_recipients ?? [];

        return Action::make('sendReportNow')
            ->label(__('Send Now'))
            ->icon('heroicon-o-paper-airplane')
            ->color('warning')
            ->schema([
                Select::make('month')
                    ->label(__('Select Month'))
                    ->options(self::getMonthOptions())
                    ->default(now()->subMonth()->format('Y-m'))
                    ->required()
                    ->native(false)
                    ->helperText(__('Choose the month for the report you want to send.')),

                TagsInput::make('recipients')
                    ->label(__('Recipients'))
                    ->default($defaultRecipients)
                    ->required()
                    ->placeholder(__('Enter email addresses'))
                    ->helperText(__('Enter email addresses, press Enter after each one.'))
                    ->nestedRecursiveRules([
                        'email',
                    ]),
            ])
            ->closeModalByClickingAway(false)
            ->action(function (array $data) use ($resolvedTeam): void {
                $month = Date::parse($data['month'] . '-01');
                $recipients = $data['recipients'] ?? [];

                if (empty($recipients)) {
                    Notification::make()
                        ->title(__('No recipients specified'))
                        ->body(__('Please enter at least one email address.'))
                        ->danger()
                        ->send();

                    return;
                }

                dispatch(new SendGoogleAdsReportNowJob(
                    teamId: $resolvedTeam->id,
                    month: $month,
                    recipients: $recipients,
                    userId: Auth::id(),
                ));

                Notification::make()
                    ->title(__('Email sending started'))
                    ->body(__('The report is being generated and will be sent to the specified addresses soon.'))
                    ->success()
                    ->send();
            });
    }

    /**
     * @return array<string, string>
     */
    private static function getMonthOptions(): array
    {
        return collect(range(0, 11))
            ->mapWithKeys(function (int $i): array {
                $date = now()->subMonths($i);

                return [$date->format('Y-m') => $date->locale('hu')->translatedFormat('Y. F')];
            })
            ->toArray();
    }
}
