<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Team;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final readonly class GoogleAdsReportData
{
    /**
     * @param  Collection<int, array<string, mixed>>  $campaigns
     * @param  Collection<int, array<string, mixed>>  $adGroups
     * @param  Collection<int, array<string, mixed>>  $dailyStats
     * @param  Collection<int, array<string, mixed>>  $hourlyStats
     * @param  Collection<int, array<string, mixed>>  $deviceStats
     * @param  Collection<int, array<string, mixed>>  $genderStats
     * @param  Collection<int, array<string, mixed>>  $ageStats
     * @param  Collection<int, array<string, mixed>>  $geographicStats
     * @param  Collection<int, array<string, mixed>>  $currentYearMonthly
     * @param  Collection<int, array<string, mixed>>  $previousYearMonthly
     * @param  array<string, mixed>  $kpiSummary
     * @param  array<string, mixed>  $previousMonthKpi
     */
    public function __construct(
        public Team $team,
        public CarbonInterface $month,
        public array $kpiSummary,
        public array $previousMonthKpi,
        public Collection $campaigns,
        public Collection $adGroups,
        public Collection $dailyStats,
        public Collection $hourlyStats,
        public Collection $deviceStats,
        public Collection $genderStats,
        public Collection $ageStats,
        public Collection $geographicStats,
        public Collection $currentYearMonthly,
        public Collection $previousYearMonthly,
    ) {}

    public function getDateRangeString(): string
    {
        $start = $this->month->copy()->startOfMonth();
        $end = $this->month->copy()->endOfMonth();

        return $start->format('Y. M j.') . ' - ' . $end->format('Y. M j.');
    }

    /**
     * @return array<string, float|string>
     */
    public function getKpiChanges(): array
    {
        $changes = [];
        $metrics = ['impressions', 'clicks', 'cost', 'conversions', 'avg_cpc', 'cost_per_conversion'];

        foreach ($metrics as $metric) {
            $current = (float) ($this->kpiSummary[$metric] ?? 0);
            $previous = (float) ($this->previousMonthKpi[$metric] ?? 0);

            if ($previous > 0) {
                $change = (($current - $previous) / $previous) * 100;
                $changes[$metric] = round($change, 1);
            } else {
                $changes[$metric] = $current > 0 ? 100.0 : 0.0;
            }
        }

        return $changes;
    }
}
