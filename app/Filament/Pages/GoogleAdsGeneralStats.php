<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Filament\Pages\Actions\SetGoogleAdsKpiGoalAction;
use App\Models\GoogleAdsAdGroup;
use App\Models\GoogleAdsCampaign;
use App\Models\GoogleAdsDemographic;
use App\Models\GoogleAdsDeviceStat;
use App\Models\GoogleAdsGeographicStat;
use App\Models\GoogleAdsHourlyStat;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use UnitEnum;

final class GoogleAdsGeneralStats extends Page
{
    public string $dateRangeType = '28_days';

    /** @var array<string, mixed> */
    public array $stats = [];

    /** @var array<int, array<string, mixed>> */
    public array $campaigns = [];

    /** @var array<int, array<string, mixed>> */
    public array $adGroups = [];

    /** @var array<int, array<string, mixed>> */
    public array $hourlyStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $deviceStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $genderStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $ageStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $geographicStats = [];

    protected string $view = 'filament.pages.google-ads-general-stats';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::GoogleAds;

    protected static ?int $navigationSort = -10;

    public static function getNavigationLabel(): string
    {
        return __('Google Ads Dashboard');
    }

    public function getTitle(): string
    {
        return __('Google Ads Dashboard');
    }

    public function mount(): void
    {
        $this->dateRangeType = Session::get('google_ads_date_range', '28_days');
        $this->loadGoogleAdsData();
    }

    public function setDateRange(string $type): void
    {
        $this->dateRangeType = $type;
        session(['google_ads_date_range' => $type]);
        $this->loadGoogleAdsData();
    }

    public function getStartDate(): CarbonInterface
    {
        return match ($this->dateRangeType) {
            '7_days' => now()->subDays(7),
            '28_days' => now()->subDays(28),
            '3_months' => now()->subMonths(3),
            default => now()->subDays(28),
        };
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            SetGoogleAdsKpiGoalAction::make(
                fn (): array => $this->campaigns,
                fn (): array => $this->adGroups,
            ),
        ];
    }

    private function loadGoogleAdsData(): void
    {
        $startDate = $this->getStartDate();

        // Load general stats
        $this->stats = [
            'total_impressions' => GoogleAdsCampaign::query()->where('date', '>=', $startDate)->sum('impressions'),
            'total_clicks' => GoogleAdsCampaign::query()->where('date', '>=', $startDate)->sum('clicks'),
            'total_cost' => GoogleAdsCampaign::query()->where('date', '>=', $startDate)->sum('cost'),
            'total_conversions' => GoogleAdsCampaign::query()->where('date', '>=', $startDate)->sum('conversions'),
            'avg_ctr' => GoogleAdsCampaign::query()->where('date', '>=', $startDate)->avg('ctr') ?? 0,
            'avg_cpc' => GoogleAdsCampaign::query()->where('date', '>=', $startDate)->avg('avg_cpc') ?? 0,
            'avg_conversion_rate' => GoogleAdsCampaign::query()->where('date', '>=', $startDate)->avg('conversion_rate') ?? 0,
        ];

        // Calculate cost per conversion
        $this->stats['cost_per_conversion'] = $this->stats['total_conversions'] > 0
            ? $this->stats['total_cost'] / $this->stats['total_conversions']
            : 0;

        // Load campaigns
        $this->campaigns = GoogleAdsCampaign::query()
            ->select(
                'campaign_id',
                'campaign_name',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
                DB::raw('AVG(ctr) as avg_ctr'),
                DB::raw('AVG(avg_cpc) as avg_cpc'),
                DB::raw('AVG(conversion_rate) as avg_conversion_rate'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('campaign_id', 'campaign_name')
            ->orderByDesc('total_clicks')
            ->limit(20)
            ->get()
            ->map(fn ($item): array => [
                'campaign_id' => $item->campaign_id,
                'campaign_name' => $item->campaign_name,
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'cost' => round((float) $item->total_cost, 2),
                'conversions' => round((float) $item->total_conversions, 2),
                'ctr' => round((float) $item->avg_ctr, 2),
                'avg_cpc' => round((float) $item->avg_cpc, 2),
                'conversion_rate' => round((float) $item->avg_conversion_rate, 2),
                'cost_per_conversion' => $item->total_conversions > 0
                    ? round($item->total_cost / $item->total_conversions, 2)
                    : 0,
            ])
            ->toArray();

        // Load ad groups
        $this->adGroups = GoogleAdsAdGroup::query()
            ->select(
                'campaign_name',
                'ad_group_id',
                'ad_group_name',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
                DB::raw('AVG(ctr) as avg_ctr'),
                DB::raw('AVG(conversion_rate) as avg_conversion_rate'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('campaign_name', 'ad_group_id', 'ad_group_name')
            ->orderByDesc('total_clicks')
            ->limit(20)
            ->get()
            ->map(fn ($item): array => [
                'campaign_name' => $item->campaign_name,
                'ad_group_name' => $item->ad_group_name,
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'cost' => round((float) $item->total_cost, 2),
                'conversions' => round((float) $item->total_conversions, 2),
                'ctr' => round((float) $item->avg_ctr, 2),
                'conversion_rate' => round((float) $item->avg_conversion_rate, 2),
                'cost_per_conversion' => $item->total_conversions > 0
                    ? round($item->total_cost / $item->total_conversions, 2)
                    : 0,
            ])
            ->toArray();

        // Load hourly stats (aggregated)
        $this->hourlyStats = GoogleAdsHourlyStat::query()
            ->select(
                'hour',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn ($item): array => [
                'hour' => $item->hour,
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'cost' => round((float) $item->total_cost, 2),
                'conversions' => round((float) $item->total_conversions, 2),
            ])
            ->toArray();

        // Load device stats
        $this->deviceStats = GoogleAdsDeviceStat::query()
            ->select(
                'device',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
                DB::raw('AVG(ctr) as avg_ctr'),
                DB::raw('AVG(conversion_rate) as avg_conversion_rate'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('device')
            ->orderByDesc('total_clicks')
            ->get()
            ->map(fn ($item): array => [
                'device' => $this->getDeviceName($item->device),
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'cost' => round((float) $item->total_cost, 2),
                'conversions' => round((float) $item->total_conversions, 2),
                'ctr' => round((float) $item->avg_ctr, 2),
                'conversion_rate' => round((float) $item->avg_conversion_rate, 2),
            ])
            ->toArray();

        // Load gender stats
        $this->genderStats = GoogleAdsDemographic::query()
            ->select(
                'gender',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
                DB::raw('AVG(conversion_rate) as avg_conversion_rate'),
            )
            ->where('date', '>=', $startDate)
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->orderByDesc('total_clicks')
            ->get()
            ->map(fn ($item): array => [
                'gender' => $this->getGenderName($item->gender),
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'cost' => round((float) $item->total_cost, 2),
                'conversions' => round((float) $item->total_conversions, 2),
                'conversion_rate' => round((float) $item->avg_conversion_rate, 2),
            ])
            ->toArray();

        // Load age stats
        $this->ageStats = GoogleAdsDemographic::query()
            ->select(
                'age_range',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
                DB::raw('AVG(conversion_rate) as avg_conversion_rate'),
            )
            ->where('date', '>=', $startDate)
            ->whereNotNull('age_range')
            ->groupBy('age_range')
            ->orderByDesc('total_clicks')
            ->get()
            ->map(fn ($item): array => [
                'age_range' => $this->getAgeRangeName($item->age_range),
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'cost' => round((float) $item->total_cost, 2),
                'conversions' => round((float) $item->total_conversions, 2),
                'conversion_rate' => round((float) $item->avg_conversion_rate, 2),
            ])
            ->toArray();

        // Load geographic stats
        $this->geographicStats = GoogleAdsGeographicStat::query()
            ->select(
                'location_name',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
                DB::raw('AVG(ctr) as avg_ctr'),
                DB::raw('AVG(conversion_rate) as avg_conversion_rate'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('location_name')
            ->orderByDesc('total_impressions')
            ->limit(20)
            ->get()
            ->map(fn ($item): array => [
                'location_name' => $item->location_name,
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'cost' => round((float) $item->total_cost, 2),
                'conversions' => round((float) $item->total_conversions, 2),
                'ctr' => round((float) $item->avg_ctr, 2),
                'conversion_rate' => round((float) $item->avg_conversion_rate, 2),
            ])
            ->toArray();
    }

    private function getDeviceName(string $device): string
    {
        return match ($device) {
            'MOBILE' => __('Mobile'),
            'DESKTOP' => __('Desktop'),
            'TABLET' => __('Tablet'),
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
            'AGE_RANGE_18_24' => '18-24',
            'AGE_RANGE_25_34' => '25-34',
            'AGE_RANGE_35_44' => '35-44',
            'AGE_RANGE_45_54' => '45-54',
            'AGE_RANGE_55_64' => '55-64',
            'AGE_RANGE_65_UP' => '65+',
            'AGE_RANGE_UNDETERMINED' => __('Undetermined'),
            default => $ageRange ?? __('Unknown'),
        };
    }
}
