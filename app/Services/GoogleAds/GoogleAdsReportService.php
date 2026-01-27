<?php

declare(strict_types=1);

namespace App\Services\GoogleAds;

use App\DataTransferObjects\GoogleAdsReportData;
use App\Models\GoogleAdsAdGroup;
use App\Models\GoogleAdsCampaign;
use App\Models\GoogleAdsDemographic;
use App\Models\GoogleAdsDeviceStat;
use App\Models\GoogleAdsGeographicStat;
use App\Models\GoogleAdsHourlyStat;
use App\Models\Team;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

final class GoogleAdsReportService
{
    public function generateReportData(Team $team, CarbonInterface $month): GoogleAdsReportData
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $previousMonthStart = $month->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $month->copy()->subMonth()->endOfMonth();

        return new GoogleAdsReportData(
            team: $team,
            month: $month,
            kpiSummary: $this->getKpiSummary($team, $startDate, $endDate),
            previousMonthKpi: $this->getKpiSummary($team, $previousMonthStart, $previousMonthEnd),
            campaigns: $this->getCampaigns($team, $startDate, $endDate),
            adGroups: $this->getAdGroups($team, $startDate, $endDate),
            dailyStats: $this->getDailyStats($team, $startDate, $endDate),
            hourlyStats: $this->getHourlyStats($team, $startDate, $endDate),
            deviceStats: $this->getDeviceStats($team, $startDate, $endDate),
            genderStats: $this->getGenderStats($team, $startDate, $endDate),
            ageStats: $this->getAgeStats($team, $startDate, $endDate),
            geographicStats: $this->getGeographicStats($team, $startDate, $endDate),
            currentYearMonthly: $this->getMonthlyStats($team, $month->copy()->startOfYear(), $endDate),
            previousYearMonthly: $this->getMonthlyStats(
                $team,
                $month->copy()->subYear()->startOfYear(),
                $month->copy()->subYear()->endOfYear(),
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function getKpiSummary(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): array
    {
        $stats = GoogleAdsCampaign::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(SUM(impressions), 0) as impressions,
                COALESCE(SUM(clicks), 0) as clicks,
                COALESCE(SUM(cost), 0) as cost,
                COALESCE(SUM(conversions), 0) as conversions,
                COALESCE(AVG(ctr), 0) as ctr
            ')
            ->first();

        $impressions = (int) $stats->impressions;
        $clicks = (int) $stats->clicks;
        $cost = (float) $stats->cost;
        $conversions = (float) $stats->conversions;
        $ctr = (float) $stats->ctr;

        return [
            'impressions' => $impressions,
            'clicks' => $clicks,
            'cost' => round($cost, 2),
            'conversions' => round($conversions, 2),
            'ctr' => round($ctr * 100, 2),
            'avg_cpc' => $clicks > 0 ? round($cost / $clicks, 2) : 0,
            'cost_per_conversion' => $conversions > 0 ? round($cost / $conversions, 2) : 0,
            'conversion_rate' => $clicks > 0 ? round(($conversions / $clicks) * 100, 2) : 0,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getCampaigns(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsCampaign::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'campaign_id',
                'campaign_name',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('campaign_id', 'campaign_name')
            ->orderByDesc('impressions')
            ->get()
            ->map(fn ($item): array => [
                'campaign_name' => $item->campaign_name,
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getAdGroups(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsAdGroup::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'campaign_name',
                'ad_group_name',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('campaign_name', 'ad_group_name')
            ->orderByDesc('impressions')
            ->get()
            ->map(fn ($item): array => [
                'campaign_name' => $item->campaign_name,
                'ad_group_name' => $item->ad_group_name,
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getDailyStats(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsCampaign::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'date',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($item): array => [
                'date' => Date::parse($item->date),
                'day_name' => Date::parse($item->date)->locale('hu')->dayName,
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getHourlyStats(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsHourlyStat::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'hour',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn ($item): array => [
                'hour' => (int) $item->hour,
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getDeviceStats(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsDeviceStat::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'device',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('device')
            ->orderByDesc('impressions')
            ->get()
            ->map(fn ($item): array => [
                'device' => $this->getDeviceName($item->device),
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getGenderStats(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsDemographic::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('gender')
            ->select(
                'gender',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('gender')
            ->orderByDesc('impressions')
            ->get()
            ->map(fn ($item): array => [
                'gender' => $this->getGenderName($item->gender),
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getAgeStats(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsDemographic::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('age_range')
            ->select(
                'age_range',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('age_range')
            ->orderByDesc('impressions')
            ->get()
            ->map(fn ($item): array => [
                'age_range' => $this->getAgeRangeName($item->age_range),
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getGeographicStats(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        return GoogleAdsGeographicStat::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'location_name',
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(conversions) as conversions'),
                DB::raw('AVG(ctr) as ctr'),
                DB::raw('AVG(conversion_rate) as conversion_rate'),
            )
            ->groupBy('location_name')
            ->orderByDesc('impressions')
            ->get()
            ->map(fn ($item): array => [
                'location_name' => $item->location_name,
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getMonthlyStats(Team $team, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? 'strftime(\'%Y-%m\', date)'
            : 'DATE_FORMAT(date, \'%Y-%m\')';

        return GoogleAdsCampaign::query()
            ->where('team_id', $team->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw("
                {$monthExpr} as month,
                SUM(impressions) as impressions,
                SUM(clicks) as clicks,
                SUM(cost) as cost,
                SUM(conversions) as conversions,
                AVG(ctr) as ctr,
                AVG(conversion_rate) as conversion_rate
            ")
            ->groupByRaw($monthExpr)
            ->orderByRaw("{$monthExpr} DESC")
            ->get()
            ->map(fn ($item): array => [
                'month' => Date::parse($item->month . '-01'),
                'month_name' => Date::parse($item->month . '-01')->locale('hu')->monthName,
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'cost' => round((float) $item->cost, 2),
                'conversions' => round((float) $item->conversions, 2),
                'ctr' => round((float) $item->ctr * 100, 2),
                'avg_cpc' => $item->clicks > 0 ? round($item->cost / $item->clicks, 2) : 0,
                'cost_per_conversion' => $item->conversions > 0 ? round($item->cost / $item->conversions, 2) : 0,
                'conversion_rate' => round((float) $item->conversion_rate * 100, 2),
            ]);
    }

    private function getDeviceName(string $device): string
    {
        return match ($device) {
            'MOBILE' => __('mobile devices with full browsers'),
            'DESKTOP' => __('computers'),
            'TABLET' => __('tablets with full browsers'),
            'CONNECTED_TV' => __('Connected TV'),
            default => $device,
        };
    }

    private function getGenderName(?string $gender): string
    {
        return match ($gender) {
            'MALE' => __('Male'),
            'FEMALE' => __('Female'),
            'UNDETERMINED' => __('Undetermined'),
            default => $gender ?? __('Unknown'),
        };
    }

    private function getAgeRangeName(?string $ageRange): string
    {
        return match ($ageRange) {
            'AGE_RANGE_18_24' => '18to24',
            'AGE_RANGE_25_34' => '25to34',
            'AGE_RANGE_35_44' => '35to44',
            'AGE_RANGE_45_54' => '45to54',
            'AGE_RANGE_55_64' => '55to64',
            'AGE_RANGE_65_UP' => 'gt64',
            'AGE_RANGE_UNDETERMINED' => __('Undetermined'),
            default => $ageRange ?? __('Unknown'),
        };
    }
}
