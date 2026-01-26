<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\AnalyticsSession;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class AnalyticsChartWidget extends ChartWidget
{
    public ?int $teamId = null;

    protected ?string $heading = 'Analytics - Sessions & Users';

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

        $data = AnalyticsSession::query()
            ->where('team_id', $this->teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select([
                DB::raw('date'),
                DB::raw('SUM(sessions) as sessions'),
                DB::raw('SUM(users) as users'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('Sessions'),
                    'data' => $data->pluck('sessions')->map(fn ($v) => (int) $v)->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => __('Users'),
                    'data' => $data->pluck('users')->map(fn ($v) => (int) $v)->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
