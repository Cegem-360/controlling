<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\SearchQuery;
use Carbon\Month;
use Carbon\WeekDay;
use DateTimeInterface;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Override;

final class SearchConsoleChartWidget extends ChartWidget
{
    public ?int $teamId = null;

    protected ?string $heading = 'Search Console - Clicks & Impressions';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    #[Override]
    protected function getData(): array
    {
        if (! $this->teamId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $endDate = Date::today();
        $startDate = $endDate->copy()->subDays(14);

        $data = SearchQuery::query()
            ->where('team_id', $this->teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select([
                DB::raw('date'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(impressions) as impressions'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('Clicks'),
                    'data' => $data->pluck('clicks')->map(fn ($v): int => (int) $v)->toArray(),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                ],
                [
                    'label' => __('Impressions'),
                    'data' => $data->pluck('impressions')->map(fn ($v): int => (int) $v)->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                ],
            ],
            'labels' => $data->pluck('date')->map(fn (DateTimeInterface|WeekDay|Month|string|int|float|null $d): string => Date::parse($d)->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
