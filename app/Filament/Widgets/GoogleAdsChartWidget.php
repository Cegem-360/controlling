<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\GoogleAdsCampaign;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class GoogleAdsChartWidget extends ChartWidget
{
    public ?int $teamId = null;

    protected ?string $heading = 'Google Ads - Cost & Conversions';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        if (! $this->teamId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(14);

        $data = GoogleAdsCampaign::query()
            ->where('team_id', $this->teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select([
                DB::raw('date'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(conversions) as conversions'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('Cost (Ft)'),
                    'data' => $data->pluck('cost')->map(fn ($v) => round((float) $v, 0))->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => __('Conversions'),
                    'data' => $data->pluck('conversions')->map(fn ($v) => round((float) $v, 0))->toArray(),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('M d'))->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
