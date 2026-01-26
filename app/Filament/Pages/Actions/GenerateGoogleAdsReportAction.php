<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Jobs\GenerateGoogleAdsReportJob;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

final class GenerateGoogleAdsReportAction
{
    /**
     * Create the generate report action.
     *
     * @param  Team|null  $team  When provided, uses this team. Otherwise falls back to Filament tenant.
     */
    public static function make(?Team $team = null): Action
    {
        return Action::make('generateReport')
            ->label(__('Havi riport generálása'))
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->schema([
                Select::make('month')
                    ->label(__('Válassz hónapot'))
                    ->options(self::getMonthOptions())
                    ->default(now()->subMonth()->format('Y-m'))
                    ->required()
                    ->native(false)
                    ->helperText(__('Válaszd ki a hónapot, amire a riportot szeretnéd generálni.')),
            ])
            ->closeModalByClickingAway(false)
            ->action(function (array $data) use ($team): void {
                $month = Date::parse($data['month'] . '-01');
                $resolvedTeam = $team ?? Filament::getTenant();

                dispatch(new GenerateGoogleAdsReportJob(
                    teamId: $resolvedTeam->id,
                    month: $month,
                    userId: Auth::id(),
                ));

                Notification::make()
                    ->title(__('Riport generálása elindult'))
                    ->body(__('Értesítünk, ha elkészült a PDF.'))
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
