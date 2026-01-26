<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AnalyticsPageview;
use App\Models\AnalyticsSession;
use App\Models\GoogleAdsCampaign;
use App\Models\Kpi;
use App\Models\SearchPage;
use App\Models\SearchQuery;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class Dashboard extends Component
{
    /** @var Collection<int, Team> */
    public $teams;

    public ?Team $currentTeam = null;

    /** @var array<string, mixed> */
    public array $analyticsStats = [];

    /** @var array<string, mixed> */
    public array $searchConsoleStats = [];

    /** @var array<string, mixed> */
    public array $googleAdsStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $kpiStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $analyticsChartData = [];

    /** @var array<int, array<string, mixed>> */
    public array $searchConsoleChartData = [];

    /** @var array<int, array<string, mixed>> */
    public array $googleAdsChartData = [];

    public function mount(): void
    {
        $this->teams = Auth::user()->teams()->with(['settings', 'googleAdsSettings'])->get();
        $this->currentTeam = $this->teams->first();

        if ($this->currentTeam instanceof Team) {
            $this->loadStatistics();
        }
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }

    private function loadStatistics(): void
    {
        $teamId = $this->currentTeam?->id;

        if (! $teamId) {
            return;
        }

        $this->loadAnalyticsStats($teamId);
        $this->loadSearchConsoleStats($teamId);
        $this->loadGoogleAdsStats($teamId);
        $this->loadKpiStats($teamId);
    }

    private function loadAnalyticsStats(int $teamId): void
    {
        $endDate = Date::today();
        $startDate = $endDate->copy()->subDays(30);
        $prevStartDate = $startDate->copy()->subDays(30);
        $prevEndDate = $startDate->copy()->subDay();

        // Current period totals
        $currentSessions = AnalyticsSession::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('sessions');

        $currentUsers = AnalyticsSession::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('users');

        $currentPageviews = AnalyticsPageview::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('pageviews');

        $currentBounceRate = AnalyticsSession::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->avg('bounce_rate') ?? 0;

        // Previous period totals for comparison
        $prevSessions = AnalyticsSession::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('sessions');

        $prevUsers = AnalyticsSession::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('users');

        $prevPageviews = AnalyticsPageview::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('pageviews');

        $this->analyticsStats = [
            'sessions' => [
                'value' => (int) $currentSessions,
                'change' => $this->calculateChange((float) $currentSessions, (float) $prevSessions),
            ],
            'users' => [
                'value' => (int) $currentUsers,
                'change' => $this->calculateChange((float) $currentUsers, (float) $prevUsers),
            ],
            'pageviews' => [
                'value' => (int) $currentPageviews,
                'change' => $this->calculateChange((float) $currentPageviews, (float) $prevPageviews),
            ],
            'bounce_rate' => [
                'value' => round((float) $currentBounceRate, 1),
                'change' => 0,
            ],
        ];

        // Daily data for chart (last 14 days)
        $this->analyticsChartData = AnalyticsSession::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$endDate->copy()->subDays(14), $endDate])
            ->select([
                DB::raw('date'),
                DB::raw('SUM(sessions) as sessions'),
                DB::raw('SUM(users) as users'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row): array => [
                'date' => Date::parse($row->date)->format('M d'),
                'sessions' => (int) $row->sessions,
                'users' => (int) $row->users,
            ])
            ->toArray();
    }

    private function loadSearchConsoleStats(int $teamId): void
    {
        $endDate = Date::today();
        $startDate = $endDate->copy()->subDays(30);
        $prevStartDate = $startDate->copy()->subDays(30);
        $prevEndDate = $startDate->copy()->subDay();

        // Current period
        $currentClicks = SearchQuery::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('clicks');

        $currentImpressions = SearchQuery::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('impressions');

        $currentCtr = SearchQuery::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->avg('ctr') ?? 0;

        $currentPosition = SearchQuery::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->avg('position') ?? 0;

        // Previous period
        $prevClicks = SearchQuery::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('clicks');

        $prevImpressions = SearchQuery::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('impressions');

        $this->searchConsoleStats = [
            'clicks' => [
                'value' => (int) $currentClicks,
                'change' => $this->calculateChange((float) $currentClicks, (float) $prevClicks),
            ],
            'impressions' => [
                'value' => (int) $currentImpressions,
                'change' => $this->calculateChange((float) $currentImpressions, (float) $prevImpressions),
            ],
            'ctr' => [
                'value' => round((float) $currentCtr * 100, 2),
                'change' => 0,
            ],
            'position' => [
                'value' => round((float) $currentPosition, 1),
                'change' => 0,
            ],
        ];

        // Daily data for chart
        $this->searchConsoleChartData = SearchQuery::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$endDate->copy()->subDays(14), $endDate])
            ->select([
                DB::raw('date'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(impressions) as impressions'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row): array => [
                'date' => Date::parse($row->date)->format('M d'),
                'clicks' => (int) $row->clicks,
                'impressions' => (int) $row->impressions,
            ])
            ->toArray();
    }

    private function loadGoogleAdsStats(int $teamId): void
    {
        $endDate = Date::today();
        $startDate = $endDate->copy()->subDays(30);
        $prevStartDate = $startDate->copy()->subDays(30);
        $prevEndDate = $startDate->copy()->subDay();

        // Current period
        $currentCost = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('cost');

        $currentClicks = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('clicks');

        $currentImpressions = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('impressions');

        $currentConversions = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('conversions');

        // Previous period
        $prevCost = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('cost');

        $prevClicks = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('clicks');

        $prevConversions = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->sum('conversions');

        $this->googleAdsStats = [
            'cost' => [
                'value' => round((float) $currentCost, 0),
                'change' => $this->calculateChange((float) $currentCost, (float) $prevCost),
            ],
            'clicks' => [
                'value' => (int) $currentClicks,
                'change' => $this->calculateChange((float) $currentClicks, (float) $prevClicks),
            ],
            'impressions' => [
                'value' => (int) $currentImpressions,
                'change' => 0,
            ],
            'conversions' => [
                'value' => round((float) $currentConversions, 0),
                'change' => $this->calculateChange((float) $currentConversions, (float) $prevConversions),
            ],
        ];

        // Daily data for chart
        $this->googleAdsChartData = GoogleAdsCampaign::query()
            ->where('team_id', $teamId)
            ->whereBetween('date', [$endDate->copy()->subDays(14), $endDate])
            ->select([
                DB::raw('date'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(clicks) as clicks'),
                DB::raw('SUM(conversions) as conversions'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row): array => [
                'date' => Date::parse($row->date)->format('M d'),
                'cost' => round((float) $row->cost, 0),
                'clicks' => (int) $row->clicks,
                'conversions' => round((float) $row->conversions, 0),
            ])
            ->toArray();
    }

    private function loadKpiStats(int $teamId): void
    {
        $kpis = Kpi::query()
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $this->kpiStats = $kpis->map(function (Kpi $kpi): array {
            $currentValue = $this->getKpiCurrentValue($kpi);
            $targetValue = (float) $kpi->target_value;
            $progress = $targetValue > 0 ? min(100, ($currentValue / $targetValue) * 100) : 0;

            return [
                'id' => $kpi->id,
                'name' => $kpi->name,
                'current_value' => $currentValue,
                'target_value' => $targetValue,
                'progress' => round($progress, 1),
                'category' => $kpi->category?->value ?? 'general',
                'format' => $kpi->format ?? 'number',
                'days_remaining' => $kpi->target_date ? (int) now()->diffInDays($kpi->target_date, false) : null,
            ];
        })->toArray();
    }

    private function getKpiCurrentValue(Kpi $kpi): float
    {
        if (! $kpi->from_date || ! $kpi->target_date || ! $kpi->metric_type) {
            return 0;
        }

        $startDate = $kpi->from_date;
        $endDate = min($kpi->target_date, now());

        return match ($kpi->data_source?->value) {
            'search_console' => $this->getSearchConsoleKpiValue($kpi, $startDate, $endDate),
            'analytics' => $this->getAnalyticsKpiValue($kpi, $startDate, $endDate),
            'google_ads' => $this->getGoogleAdsKpiValue($kpi, $startDate, $endDate),
            default => 0,
        };
    }

    private function getSearchConsoleKpiValue(Kpi $kpi, $startDate, $endDate): float
    {
        $query = $kpi->source_type === 'query'
            ? SearchQuery::query()->where('query', $kpi->page_path)
            : SearchPage::query()->where('page_url', $kpi->page_path);

        $query->where('team_id', $kpi->team_id)
            ->whereBetween('date', [$startDate, $endDate]);

        return in_array($kpi->metric_type, ['ctr', 'position'])
            ? (float) ($query->avg($kpi->metric_type) ?? 0)
            : (float) ($query->sum($kpi->metric_type) ?? 0);
    }

    private function getAnalyticsKpiValue(Kpi $kpi, $startDate, $endDate): float
    {
        $query = AnalyticsPageview::query()
            ->where('team_id', $kpi->team_id)
            ->where('page_path', $kpi->page_path)
            ->whereBetween('date', [$startDate, $endDate]);

        return $kpi->metric_type === 'bounce_rate'
            ? (float) ($query->avg($kpi->metric_type) ?? 0)
            : (float) ($query->sum($kpi->metric_type) ?? 0);
    }

    private function getGoogleAdsKpiValue(Kpi $kpi, $startDate, $endDate): float
    {
        $query = $kpi->source_type === 'campaign'
            ? GoogleAdsCampaign::query()->where('campaign_id', $kpi->page_path)
            : GoogleAdsCampaign::query()->where('campaign_name', $kpi->page_path);

        $query->where('team_id', $kpi->team_id)
            ->whereBetween('date', [$startDate, $endDate]);

        return in_array($kpi->metric_type, ['ctr', 'conversion_rate', 'avg_cpc', 'cost_per_conversion'])
            ? (float) ($query->avg($kpi->metric_type) ?? 0)
            : (float) ($query->sum($kpi->metric_type) ?? 0);
    }

    private function calculateChange(float|int $current, float|int $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
