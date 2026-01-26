<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Jobs\GenerateGoogleAdsReportJob;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

final class GenerateGoogleAdsReportAction
{
    public static function make(): Action
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
            ->action(function (array $data): void {
                $month = Carbon::parse($data['month'] . '-01');
                $team = Filament::getTenant();

                GenerateGoogleAdsReportJob::dispatch(
                    teamId: $team->id,
                    month: $month,
                    userId: auth()->id(),
                );

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
        $options = [];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->locale('hu')->translatedFormat('Y. F');
            $options[$key] = $label;
        }

        return $options;
    }
}
