<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kpis\Widgets;

use App\Enums\KpiDataSource;
use App\Models\AnalyticsPageview;
use App\Models\Kpi;
use App\Models\SearchPage;
use App\Models\SearchQuery;
use Carbon\CarbonInterface;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class KpiValuesChartWidget extends ChartWidget
{
    private const MAX_DAYS = 365;

    public ?Model $record = null;

    public function getHeading(): string
    {
        return 'KPI Progress Over Time';
    }

    public function getDescription(): ?string
    {
        if (! $this->record instanceof Kpi) {
            return null;
        }

        $kpi = $this->record;

        $missingFields = array_filter([
            $kpi->from_date ? null : 'Start Date',
            $kpi->target_date ? null : 'Target Date',
        ]);

        if ($missingFields !== []) {
            return 'Missing required field(s): ' . implode(', ', $missingFields) . '.';
        }

        if ($kpi->from_date->gt($kpi->target_date)) {
            return 'Invalid date range: Start date must be before target date.';
        }

        if ($kpi->from_date->diffInDays($kpi->target_date) > self::MAX_DAYS) {
            return 'Date range too large (max 365 days). Please adjust your date range.';
        }

        return null;
    }

    protected function getData(): array
    {
        $emptyResult = ['datasets' => [], 'labels' => []];

        if (! $this->record instanceof Kpi) {
            return $emptyResult;
        }

        $kpi = $this->record;

        if (! $this->isValidKpiConfiguration($kpi)) {
            return $emptyResult;
        }

        $fullDateRange = $this->generateDateRange($kpi);
        $sourceData = $this->fetchSourceData($kpi);
        $labels = $this->buildLabels($fullDateRange);
        $actualData = $this->buildActualData($kpi, $fullDateRange, $sourceData);
        $metricLabel = $this->getMetricLabel($kpi);

        $datasets = [
            $this->buildCurrentDataset($metricLabel, $actualData, $kpi->data_source),
        ];

        if ($kpi->comparison_start_date && $kpi->comparison_end_date) {
            $comparisonData = $this->fetchComparisonData($kpi);
            $comparisonValues = $this->buildComparisonData($kpi, $fullDateRange, $comparisonData);
            $datasets[] = $this->buildComparisonDataset($metricLabel, $comparisonValues);

            // Add target line (average of comparison period)
            $comparisonTotal = $this->calculateComparisonTotal($kpi, $comparisonData);
            if ($comparisonTotal > 0) {
                $datasets[] = $this->buildTargetDataset($kpi, $actualData, $comparisonTotal);
            }
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true],
                'x' => [
                    'display' => true,
                    'grid' => ['display' => true],
                ],
            ],
            'elements' => [
                'line' => ['spanGaps' => true],
                'point' => [
                    'radius' => 3,
                    'hoverRadius' => 5,
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }

    private function isValidKpiConfiguration(Kpi $kpi): bool
    {
        if (! $kpi->from_date || ! $kpi->target_date) {
            return false;
        }

        if (! $kpi->page_path || ! $kpi->metric_type) {
            return false;
        }

        if ($kpi->from_date->gt($kpi->target_date)) {
            return false;
        }

        if ($kpi->from_date->diffInDays($kpi->target_date) > self::MAX_DAYS) {
            return false;
        }

        return KpiDataSource::isIntegrationSource($kpi->data_source);
    }

    /**
     * @return array<CarbonInterface>
     */
    private function generateDateRange(Kpi $kpi): array
    {
        $startDate = $kpi->from_date;
        $endDate = $kpi->target_date;

        if ($kpi->comparison_start_date && $kpi->comparison_end_date) {
            $startDate = min($kpi->from_date, $kpi->comparison_start_date);
            $endDate = max($kpi->target_date, $kpi->comparison_end_date);
        }

        $maxDays = min($startDate->diffInDays($endDate) + 1, self::MAX_DAYS);
        $dates = [];

        for ($i = 0; $i < $maxDays; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }

        return $dates;
    }

    private function fetchSourceData(Kpi $kpi): Collection
    {
        $query = match ($kpi->data_source) {
            KpiDataSource::SearchConsole => $kpi->source_type === 'query'
                ? SearchQuery::query()->where('query', $kpi->page_path)
                : SearchPage::query()->where('page_url', $kpi->page_path),
            KpiDataSource::Analytics => AnalyticsPageview::query()->where('page_path', $kpi->page_path),
            default => null,
        };

        if (! $query) {
            return collect();
        }

        return $query
            ->where('team_id', $kpi->team_id)
            ->whereBetween('date', [$kpi->from_date, $kpi->target_date])
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($record) => $record->date->format('Y-m-d'));
    }

    private function fetchComparisonData(Kpi $kpi): Collection
    {
        $query = match ($kpi->data_source) {
            KpiDataSource::SearchConsole => $kpi->source_type === 'query'
                ? SearchQuery::query()->where('query', $kpi->page_path)
                : SearchPage::query()->where('page_url', $kpi->page_path),
            KpiDataSource::Analytics => AnalyticsPageview::query()->where('page_path', $kpi->page_path),
            default => null,
        };

        if (! $query) {
            return collect();
        }

        return $query
            ->where('team_id', $kpi->team_id)
            ->whereBetween('date', [$kpi->comparison_start_date, $kpi->comparison_end_date])
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
    private function buildActualData(Kpi $kpi, array $fullDateRange, Collection $sourceData): array
    {
        return array_map(function (CarbonInterface $date) use ($kpi, $sourceData) {
            $dateKey = $date->format('Y-m-d');

            if ($date < $kpi->from_date || $date > $kpi->target_date || ! isset($sourceData[$dateKey])) {
                return;
            }

            return $this->extractMetricValue($sourceData[$dateKey], $kpi->metric_type);
        }, $fullDateRange);
    }

    /**
     * @param  array<CarbonInterface>  $fullDateRange
     * @return array<float|null>
     */
    private function buildComparisonData(Kpi $kpi, array $fullDateRange, Collection $comparisonData): array
    {
        return array_map(function (CarbonInterface $date) use ($kpi, $comparisonData) {
            $dateKey = $date->format('Y-m-d');

            if ($date < $kpi->comparison_start_date || $date > $kpi->comparison_end_date || ! isset($comparisonData[$dateKey])) {
                return;
            }

            return $this->extractMetricValue($comparisonData[$dateKey], $kpi->metric_type);
        }, $fullDateRange);
    }

    private function extractMetricValue(mixed $record, string $metricType): float
    {
        return (float) match ($metricType) {
            'impressions' => $record->impressions,
            'clicks' => $record->clicks,
            'ctr' => $record->ctr,
            'position' => $record->position,
            'pageviews' => $record->pageviews,
            'unique_pageviews' => $record->unique_pageviews,
            'bounce_rate' => $record->bounce_rate,
            default => 0,
        };
    }

    private function getMetricLabel(Kpi $kpi): string
    {
        return match ($kpi->metric_type) {
            'impressions' => __('Impressions'),
            'clicks' => __('Clicks'),
            'ctr' => __('CTR (%)'),
            'position' => __('Position'),
            'pageviews' => __('Pageviews'),
            'unique_pageviews' => __('Unique Pageviews'),
            'bounce_rate' => __('Bounce Rate'),
            default => __('Value'),
        };
    }

    /**
     * @param  array<float|null>  $data
     * @return array<string, mixed>
     */
    private function buildCurrentDataset(string $label, array $data, KpiDataSource $dataSource): array
    {
        $color = $dataSource === KpiDataSource::SearchConsole
            ? 'rgb(59, 130, 246)'
            : 'rgb(34, 197, 94)';

        return [
            'label' => $label . ' (Current)',
            'data' => $data,
            'borderColor' => $color,
            'backgroundColor' => str_replace('rgb', 'rgba', $color) . ', 0.1)',
            'tension' => 0.3,
        ];
    }

    private function calculateComparisonTotal(Kpi $kpi, Collection $comparisonData): float
    {
        $metricType = $kpi->metric_type;

        // For metrics that should be averaged (like CTR, position, bounce_rate)
        if (in_array($metricType, ['ctr', 'position', 'bounce_rate'])) {
            $values = $comparisonData->map(fn ($record): float => $this->extractMetricValue($record, $metricType));

            return $values->count() > 0 ? $values->avg() : 0;
        }

        // For metrics that should be summed
        return $comparisonData->sum(fn ($record): float => $this->extractMetricValue($record, $metricType));
    }

    /**
     * @param  array<float|null>  $actualData
     * @return array<string, mixed>
     */
    private function buildTargetDataset(Kpi $kpi, array $actualData, float $targetValue): array
    {
        // Create a flat line at the target value for all current period data points
        $targetData = array_map(function (?float $value) use ($targetValue) {
            if ($value === null) {
                return;
            }

            return $targetValue;
        }, $actualData);

        $color = $kpi->data_source === KpiDataSource::SearchConsole
            ? 'rgb(34, 197, 94)'
            : 'rgb(59, 130, 246)';

        return [
            'label' => 'Target',
            'data' => $targetData,
            'borderColor' => $color,
            'backgroundColor' => 'transparent',
            'borderDash' => [5, 5],
            'tension' => 0,
            'pointRadius' => 0,
        ];
    }

    /**
     * @param  array<float|null>  $data
     * @return array<string, mixed>
     */
    private function buildComparisonDataset(string $label, array $data): array
    {
        return [
            'label' => $label . ' (Comparison)',
            'data' => $data,
            'borderColor' => 'rgb(168, 85, 247)',
            'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
            'tension' => 0.3,
        ];
    }
}
