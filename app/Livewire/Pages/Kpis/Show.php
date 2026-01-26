<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Kpis;

use App\Enums\KpiDataSource;
use App\Models\AnalyticsPageview;
use App\Models\GoogleAdsAdGroup;
use App\Models\GoogleAdsCampaign;
use App\Models\Kpi;
use App\Models\SearchPage;
use App\Models\SearchQuery;
use App\Models\Team;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class Show extends Component
{
    public ?Team $team = null;

    public ?Kpi $kpi = null;

    /** @var array<string, mixed> */
    public array $stats = [];

    /** @var array<string, mixed> */
    public array $chartData = [];

    public function mount(Kpi $kpi): void
    {
        $this->team = Auth::user()->teams()->first();

        if (! $this->team instanceof Team) {
            return;
        }

        // Verify the KPI belongs to the current team
        if ($kpi->team_id !== $this->team->id) {
            abort(403);
        }

        $this->kpi = $kpi;
        $this->loadStats();
        $this->loadChartData();
    }

    public function render(): View
    {
        return view('livewire.pages.kpis.show');
    }

    public function getDataSourceColorClass(): string
    {
        if (! $this->kpi instanceof Kpi) {
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-300';
        }

        return match ($this->kpi->data_source) {
            KpiDataSource::Analytics => 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300',
            KpiDataSource::SearchConsole => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
            KpiDataSource::GoogleAds => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-300',
        };
    }

    public function getDataSourceLabel(): string
    {
        if (! $this->kpi instanceof Kpi) {
            return '';
        }

        return match ($this->kpi->data_source) {
            KpiDataSource::Analytics => __('Google Analytics'),
            KpiDataSource::SearchConsole => __('Search Console'),
            KpiDataSource::GoogleAds => __('Google Ads'),
            KpiDataSource::Manual => __('Manual'),
            KpiDataSource::Calculated => __('Calculated'),
            default => '',
        };
    }

    public function getMetricLabel(): string
    {
        if (! $this->kpi instanceof Kpi || ! $this->kpi->metric_type) {
            return __('Value');
        }

        return match ($this->kpi->metric_type) {
            'impressions' => __('Impressions'),
            'clicks' => __('Clicks'),
            'ctr' => __('CTR (%)'),
            'position' => __('Position'),
            'pageviews' => __('Pageviews'),
            'unique_pageviews' => __('Unique Pageviews'),
            'bounce_rate' => __('Bounce Rate'),
            'cost' => __('Cost'),
            'conversions' => __('Conversions'),
            'avg_cpc' => __('Avg. CPC'),
            'conversion_rate' => __('Conversion Rate (%)'),
            'cost_per_conversion' => __('Cost per Conversion'),
            default => __('Value'),
        };
    }

    private function loadStats(): void
    {
        if (! $this->kpi instanceof Kpi) {
            return;
        }

        $currentValue = $this->getCurrentValue();
        $comparisonValue = $this->getComparisonValue();

        $changePercentage = 0;
        if ($comparisonValue > 0) {
            $changePercentage = (($currentValue - $comparisonValue) / $comparisonValue) * 100;
        }

        $progress = $comparisonValue > 0 ? ($currentValue / $comparisonValue) * 100 : 0;
        $daysUntilTarget = $this->kpi->target_date ? (int) now()->diffInDays($this->kpi->target_date, false) : null;

        $this->stats = [
            'current_value' => $currentValue,
            'comparison_value' => $comparisonValue,
            'change_percentage' => $changePercentage,
            'progress' => $progress,
            'days_until_target' => $daysUntilTarget,
            'target_achieved' => $progress >= 100,
        ];
    }

    private function getCurrentValue(): float
    {
        if (! $this->kpi->page_path && ! $this->kpi->metric_type) {
            return 0;
        }

        if (! $this->kpi->from_date || ! $this->kpi->target_date) {
            return 0;
        }

        return $this->getValueForPeriod($this->kpi->from_date, $this->kpi->target_date);
    }

    private function getComparisonValue(): float
    {
        if (! $this->kpi->page_path && ! $this->kpi->metric_type) {
            return 0;
        }

        if (! $this->kpi->comparison_start_date || ! $this->kpi->comparison_end_date) {
            return 0;
        }

        return $this->getValueForPeriod($this->kpi->comparison_start_date, $this->kpi->comparison_end_date);
    }

    private function getValueForPeriod(DateTimeInterface $startDate, DateTimeInterface $endDate): float
    {
        if ($this->kpi->data_source->value === 'search_console') {
            if ($this->kpi->source_type === 'query') {
                $query = SearchQuery::query()
                    ->where('team_id', $this->kpi->team_id)
                    ->where('query', $this->kpi->page_path)
                    ->whereBetween('date', [$startDate, $endDate]);
            } else {
                $query = SearchPage::query()
                    ->where('team_id', $this->kpi->team_id)
                    ->where('page_url', $this->kpi->page_path)
                    ->whereBetween('date', [$startDate, $endDate]);
            }

            if (in_array($this->kpi->metric_type, ['ctr', 'position'], true)) {
                return (float) $query->avg($this->kpi->metric_type) ?? 0;
            }

            return (float) $query->sum($this->kpi->metric_type) ?? 0;
        }

        if ($this->kpi->data_source->value === 'analytics') {
            $query = AnalyticsPageview::query()
                ->where('team_id', $this->kpi->team_id)
                ->where('page_path', $this->kpi->page_path)
                ->whereBetween('date', [$startDate, $endDate]);

            if ($this->kpi->metric_type === 'bounce_rate') {
                return (float) $query->avg($this->kpi->metric_type) ?? 0;
            }

            return (float) $query->sum($this->kpi->metric_type) ?? 0;
        }

        if ($this->kpi->data_source->value === 'google_ads') {
            if ($this->kpi->source_type === 'campaign') {
                $query = GoogleAdsCampaign::query()
                    ->where('team_id', $this->kpi->team_id)
                    ->where('campaign_id', $this->kpi->page_path)
                    ->whereBetween('date', [$startDate, $endDate]);
            } else {
                $query = GoogleAdsAdGroup::query()
                    ->where('team_id', $this->kpi->team_id)
                    ->where('ad_group_name', $this->kpi->page_path)
                    ->whereBetween('date', [$startDate, $endDate]);
            }

            if (in_array($this->kpi->metric_type, ['ctr', 'conversion_rate', 'avg_cpc', 'cost_per_conversion'], true)) {
                return (float) $query->avg($this->kpi->metric_type) ?? 0;
            }

            return (float) $query->sum($this->kpi->metric_type) ?? 0;
        }

        return 0;
    }

    private function loadChartData(): void
    {
        if (! $this->kpi instanceof Kpi) {
            $this->chartData = ['labels' => [], 'datasets' => []];

            return;
        }

        if (! $this->isValidKpiConfiguration()) {
            $this->chartData = ['labels' => [], 'datasets' => []];

            return;
        }

        $fullDateRange = $this->generateDateRange();
        $sourceData = $this->fetchSourceData();
        $labels = $this->buildLabels($fullDateRange);
        $actualData = $this->buildActualData($fullDateRange, $sourceData);

        $datasets = [
            $this->buildCurrentDataset($actualData),
        ];

        if ($this->kpi->comparison_start_date && $this->kpi->comparison_end_date) {
            $comparisonData = $this->fetchComparisonData();
            $comparisonValues = $this->buildComparisonData($fullDateRange, $comparisonData);
            $datasets[] = $this->buildComparisonDataset($comparisonValues);

            $comparisonTotal = $this->calculateComparisonTotal($comparisonData);
            if ($comparisonTotal > 0) {
                $datasets[] = $this->buildTargetDataset($actualData, $comparisonTotal);
            }
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function isValidKpiConfiguration(): bool
    {
        if (! $this->kpi->from_date || ! $this->kpi->target_date) {
            return false;
        }

        if (! $this->kpi->page_path || ! $this->kpi->metric_type) {
            return false;
        }

        if ($this->kpi->from_date->gt($this->kpi->target_date)) {
            return false;
        }

        if ($this->kpi->from_date->diffInDays($this->kpi->target_date) > 365) {
            return false;
        }

        return KpiDataSource::isIntegrationSource($this->kpi->data_source);
    }

    /**
     * @return array<CarbonInterface>
     */
    private function generateDateRange(): array
    {
        $startDate = $this->kpi->from_date;
        $endDate = $this->kpi->target_date;

        if ($this->kpi->comparison_start_date && $this->kpi->comparison_end_date) {
            $startDate = min($this->kpi->from_date, $this->kpi->comparison_start_date);
            $endDate = max($this->kpi->target_date, $this->kpi->comparison_end_date);
        }

        $maxDays = min($startDate->diffInDays($endDate) + 1, 365);
        $dates = [];

        for ($i = 0; $i < $maxDays; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }

        return $dates;
    }

    private function fetchSourceData(): Collection
    {
        $query = match ($this->kpi->data_source) {
            KpiDataSource::SearchConsole => $this->kpi->source_type === 'query'
                ? SearchQuery::query()->where('query', $this->kpi->page_path)
                : SearchPage::query()->where('page_url', $this->kpi->page_path),
            KpiDataSource::Analytics => AnalyticsPageview::query()->where('page_path', $this->kpi->page_path),
            KpiDataSource::GoogleAds => $this->kpi->source_type === 'campaign'
                ? GoogleAdsCampaign::query()->where('campaign_id', $this->kpi->page_path)
                : GoogleAdsAdGroup::query()->where('ad_group_name', $this->kpi->page_path),
            default => null,
        };

        if (! $query) {
            return collect();
        }

        return $query
            ->where('team_id', $this->kpi->team_id)
            ->whereBetween('date', [$this->kpi->from_date, $this->kpi->target_date])
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($record) => $record->date->format('Y-m-d'));
    }

    private function fetchComparisonData(): Collection
    {
        $query = match ($this->kpi->data_source) {
            KpiDataSource::SearchConsole => $this->kpi->source_type === 'query'
                ? SearchQuery::query()->where('query', $this->kpi->page_path)
                : SearchPage::query()->where('page_url', $this->kpi->page_path),
            KpiDataSource::Analytics => AnalyticsPageview::query()->where('page_path', $this->kpi->page_path),
            KpiDataSource::GoogleAds => $this->kpi->source_type === 'campaign'
                ? GoogleAdsCampaign::query()->where('campaign_id', $this->kpi->page_path)
                : GoogleAdsAdGroup::query()->where('ad_group_name', $this->kpi->page_path),
            default => null,
        };

        if (! $query) {
            return collect();
        }

        return $query
            ->where('team_id', $this->kpi->team_id)
            ->whereBetween('date', [$this->kpi->comparison_start_date, $this->kpi->comparison_end_date])
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($record) => $record->date->format('Y-m-d'));
    }

    /**
     * @param  array<CarbonInterface>  $dates
     * @return array<string>
     */
    private function buildLabels(array $dates): array
    {
        return array_map(fn (CarbonInterface $date) => $date->format('M d'), $dates);
    }

    /**
     * @param  array<CarbonInterface>  $fullDateRange
     * @return array<float|null>
     */
    private function buildActualData(array $fullDateRange, Collection $sourceData): array
    {
        return array_map(function (CarbonInterface $date) use ($sourceData) {
            $dateKey = $date->format('Y-m-d');

            if ($date < $this->kpi->from_date || $date > $this->kpi->target_date || ! isset($sourceData[$dateKey])) {
                return;
            }

            return $this->extractMetricValue($sourceData[$dateKey]);
        }, $fullDateRange);
    }

    /**
     * @param  array<CarbonInterface>  $fullDateRange
     * @return array<float|null>
     */
    private function buildComparisonData(array $fullDateRange, Collection $comparisonData): array
    {
        return array_map(function (CarbonInterface $date) use ($comparisonData) {
            $dateKey = $date->format('Y-m-d');

            if ($date < $this->kpi->comparison_start_date || $date > $this->kpi->comparison_end_date || ! isset($comparisonData[$dateKey])) {
                return;
            }

            return $this->extractMetricValue($comparisonData[$dateKey]);
        }, $fullDateRange);
    }

    private function extractMetricValue(mixed $record): float
    {
        return (float) match ($this->kpi->metric_type) {
            'impressions' => $record->impressions,
            'clicks' => $record->clicks,
            'ctr' => $record->ctr,
            'position' => $record->position,
            'pageviews' => $record->pageviews,
            'unique_pageviews' => $record->unique_pageviews,
            'bounce_rate' => $record->bounce_rate,
            'cost' => $record->cost,
            'conversions' => $record->conversions,
            'avg_cpc' => $record->avg_cpc,
            'conversion_rate' => $record->conversion_rate,
            'cost_per_conversion' => $record->conversions > 0 ? $record->cost / $record->conversions : 0,
            default => 0,
        };
    }

    /**
     * @param  array<float|null>  $data
     * @return array<string, mixed>
     */
    private function buildCurrentDataset(array $data): array
    {
        $color = match ($this->kpi->data_source) {
            KpiDataSource::SearchConsole => '#3b82f6',
            KpiDataSource::GoogleAds => '#f97316',
            default => '#22c55e',
        };

        return [
            'label' => $this->getMetricLabel() . ' (' . __('Current') . ')',
            'data' => $data,
            'borderColor' => $color,
            'backgroundColor' => $color . '1a',
            'fill' => true,
            'tension' => 0.3,
        ];
    }

    /**
     * @param  array<float|null>  $data
     * @return array<string, mixed>
     */
    private function buildComparisonDataset(array $data): array
    {
        return [
            'label' => $this->getMetricLabel() . ' (' . __('Comparison') . ')',
            'data' => $data,
            'borderColor' => '#a855f7',
            'backgroundColor' => '#a855f71a',
            'fill' => true,
            'tension' => 0.3,
        ];
    }

    private function calculateComparisonTotal(Collection $comparisonData): float
    {
        $metricType = $this->kpi->metric_type;

        if (in_array($metricType, ['ctr', 'position', 'bounce_rate', 'conversion_rate', 'avg_cpc', 'cost_per_conversion'], true)) {
            $values = $comparisonData->map(fn ($record): float => $this->extractMetricValue($record));

            return $values->count() > 0 ? $values->avg() : 0;
        }

        return $comparisonData->sum(fn ($record): float => $this->extractMetricValue($record));
    }

    /**
     * @param  array<float|null>  $actualData
     * @return array<string, mixed>
     */
    private function buildTargetDataset(array $actualData, float $targetValue): array
    {
        $targetData = array_map(function (?float $value) use ($targetValue): ?float {
            if ($value === null) {
                return null;
            }

            return $targetValue;
        }, $actualData);

        $color = match ($this->kpi->data_source) {
            KpiDataSource::SearchConsole => '#22c55e',
            KpiDataSource::GoogleAds => '#ea580c',
            default => '#3b82f6',
        };

        return [
            'label' => __('Target'),
            'data' => $targetData,
            'borderColor' => $color,
            'backgroundColor' => 'transparent',
            'borderDash' => [5, 5],
            'tension' => 0,
            'pointRadius' => 0,
        ];
    }
}
