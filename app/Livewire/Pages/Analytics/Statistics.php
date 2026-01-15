<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Analytics;

use App\Models\AnalyticsPageview;
use App\Models\AnalyticsSession;
use App\Models\GlobalSetting;
use App\Models\Team;
use App\Services\GoogleClientFactory;
use Carbon\CarbonInterface;
use Exception;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\DateRange;
use Google\Service\AnalyticsData\Dimension;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\Row;
use Google\Service\AnalyticsData\RunReportRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Url;
use Livewire\Component;

final class Statistics extends Component
{
    public ?Team $team = null;

    public string $dateRangeType = '28_days';

    /** @var array<int, array<string, mixed>> */
    public array $topPages = [];

    /** @var array<int, array<string, mixed>> */
    public array $userSources = [];

    /** @var array<string, int|float> */
    public array $stats = [];

    // Search
    #[Url]
    public string $topPagesSearch = '';

    #[Url]
    public string $userSourcesSearch = '';

    // Sorting
    public string $topPagesSortBy = 'pageviews';

    public string $topPagesSortDir = 'desc';

    public string $userSourcesSortBy = 'sessions';

    public string $userSourcesSortDir = 'desc';

    // Pagination
    public int $topPagesPerPage = 10;

    public int $userSourcesPerPage = 10;

    public int $topPagesPage = 1;

    public int $userSourcesPage = 1;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->dateRangeType = Session::get('analytics_statistics_date_range', '28_days');
        $this->loadAnalyticsData();
    }

    public function setDateRange(string $type): void
    {
        $this->dateRangeType = $type;
        session(['analytics_statistics_date_range' => $type]);
        $this->loadAnalyticsData();
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

    public function sortTopPages(string $column): void
    {
        if ($this->topPagesSortBy === $column) {
            $this->topPagesSortDir = $this->topPagesSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->topPagesSortBy = $column;
            $this->topPagesSortDir = 'desc';
        }
        $this->topPagesPage = 1;
    }

    public function sortUserSources(string $column): void
    {
        if ($this->userSourcesSortBy === $column) {
            $this->userSourcesSortDir = $this->userSourcesSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->userSourcesSortBy = $column;
            $this->userSourcesSortDir = 'desc';
        }
        $this->userSourcesPage = 1;
    }

    public function updatedTopPagesSearch(): void
    {
        $this->topPagesPage = 1;
    }

    public function updatedUserSourcesSearch(): void
    {
        $this->userSourcesPage = 1;
    }

    public function updatedTopPagesPerPage(): void
    {
        $this->topPagesPage = 1;
    }

    public function updatedUserSourcesPerPage(): void
    {
        $this->userSourcesPage = 1;
    }

    public function render(): View
    {
        return view('livewire.pages.analytics.statistics', [
            'paginatedTopPages' => $this->getPaginatedTopPages(),
            'paginatedUserSources' => $this->getPaginatedUserSources(),
        ])->layout('components.layouts.dashboard');
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedTopPages(): array
    {
        $filtered = $this->filterData($this->topPages, $this->topPagesSearch, ['page_path', 'page_title']);
        $sorted = $this->sortData($filtered, $this->topPagesSortBy, $this->topPagesSortDir);

        return $this->paginateData($sorted, $this->topPagesPerPage, $this->topPagesPage);
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedUserSources(): array
    {
        $filtered = $this->filterData($this->userSources, $this->userSourcesSearch, ['source', 'medium']);
        $sorted = $this->sortData($filtered, $this->userSourcesSortBy, $this->userSourcesSortDir);

        return $this->paginateData($sorted, $this->userSourcesPerPage, $this->userSourcesPage);
    }

    /**
     * @param  array<int, array<string, mixed>>  $data
     * @param  array<string>  $searchFields
     * @return array<int, array<string, mixed>>
     */
    private function filterData(array $data, string $search, array $searchFields): array
    {
        if ($search === '') {
            return $data;
        }

        $search = mb_strtolower($search);

        return array_values(array_filter($data, function (array $row) use ($search, $searchFields): bool {
            foreach ($searchFields as $field) {
                if (isset($row[$field]) && str_contains(mb_strtolower((string) $row[$field]), $search)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * @param  array<int, array<string, mixed>>  $data
     * @return array<int, array<string, mixed>>
     */
    private function sortData(array $data, string $sortBy, string $sortDir): array
    {
        usort($data, function (array $a, array $b) use ($sortBy, $sortDir): int {
            $aVal = $a[$sortBy] ?? 0;
            $bVal = $b[$sortBy] ?? 0;

            $result = is_string($aVal) ? strcmp((string) $aVal, (string) $bVal) : $aVal <=> $bVal;

            return $sortDir === 'asc' ? $result : -$result;
        });

        return $data;
    }

    /**
     * @param  array<int, array<string, mixed>>  $data
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function paginateData(array $data, int $perPage, int $currentPage): array
    {
        $total = count($data);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($currentPage, $lastPage);
        $offset = ($currentPage - 1) * $perPage;

        return [
            'data' => array_slice($data, $offset, $perPage),
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
        ];
    }

    private function loadAnalyticsData(): void
    {
        $startDate = $this->getStartDate();

        // Load top pages from database
        $topPagesFromDb = AnalyticsPageview::query()
            ->where('date', '>=', $startDate)
            ->orderBy('pageviews', 'desc')
            ->limit(100)
            ->get();

        if ($topPagesFromDb->isNotEmpty()) {
            $this->topPages = $topPagesFromDb
                ->map(fn (AnalyticsPageview $page): array => [
                    'page_path' => $page->page_path,
                    'page_title' => $page->page_title,
                    'pageviews' => $page->pageviews,
                    'unique_pageviews' => $page->unique_pageviews,
                    'bounce_rate' => $page->bounce_rate,
                ])
                ->toArray();
        } else {
            // Load from Google Analytics API if no database data
            $this->loadTopPagesFromApi();
        }

        // Load user sources from Google Analytics API
        $this->loadUserSources();

        // Load overall stats
        $this->stats = [
            'total_pageviews' => AnalyticsPageview::query()->where('date', '>=', $startDate)->sum('pageviews') ?: 0,
            'total_sessions' => AnalyticsSession::query()->where('date', '>=', $startDate)->count() ?: 0,
            'avg_bounce_rate' => AnalyticsPageview::query()->where('date', '>=', $startDate)->avg('bounce_rate') ?: 0,
        ];
    }

    private function loadTopPagesFromApi(): void
    {
        $this->topPages = $this->runReport(
            dimensions: ['pagePath', 'pageTitle'],
            metrics: ['screenPageViews', 'sessions', 'bounceRate'],
            extractRow: fn (Row $row): array => [
                'page_path' => $row->getDimensionValues()[0]->getValue(),
                'page_title' => $row->getDimensionValues()[1]->getValue(),
                'pageviews' => (int) $row->getMetricValues()[0]->getValue(),
                'unique_pageviews' => (int) $row->getMetricValues()[1]->getValue(),
                'bounce_rate' => (float) $row->getMetricValues()[2]->getValue() * 100,
            ],
            sortBy: 'pageviews',
        );
    }

    private function loadUserSources(): void
    {
        $this->userSources = $this->runReport(
            dimensions: ['sessionSource', 'sessionMedium'],
            metrics: ['sessions', 'activeUsers'],
            extractRow: fn (Row $row): array => [
                'source' => $row->getDimensionValues()[0]->getValue(),
                'medium' => $row->getDimensionValues()[1]->getValue(),
                'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                'users' => (int) $row->getMetricValues()[1]->getValue(),
            ],
            sortBy: 'sessions',
        );
    }

    /**
     * @param  array<string>  $dimensions
     * @param  array<string>  $metrics
     * @param  callable(Row): array<string, mixed>  $extractRow
     * @return array<int, array<string, mixed>>
     */
    private function runReport(array $dimensions, array $metrics, callable $extractRow, string $sortBy): array
    {
        try {
            $globalSettings = GlobalSetting::instance();
            $serviceAccount = $globalSettings->getServiceAccount();

            if (! $serviceAccount) {
                return [];
            }

            $settings = $this->team?->settings;

            if (! $settings || ! $settings->property_id) {
                return [];
            }

            $client = GoogleClientFactory::make(
                'https://www.googleapis.com/auth/analytics.readonly',
                $globalSettings->google_service_account,
            );
            $service = new AnalyticsData($client);

            $dateRange = new DateRange();
            $dateRange->setStartDate($this->getStartDate()->format('Y-m-d'));
            $dateRange->setEndDate('today');

            $request = new RunReportRequest();
            $request->setDateRanges([$dateRange]);
            $request->setDimensions(array_map(function (string $name): Dimension {
                $dimension = new Dimension();
                $dimension->setName($name);

                return $dimension;
            }, $dimensions));
            $request->setMetrics(array_map(function (string $name): Metric {
                $metric = new Metric();
                $metric->setName($name);

                return $metric;
            }, $metrics));

            $response = $service->properties->runReport(
                property: "properties/{$settings->property_id}",
                postBody: $request,
            );

            $rows = array_map($extractRow, $response->getRows() ?? []);

            usort($rows, fn (array $a, array $b): int => $b[$sortBy] <=> $a[$sortBy]);

            return array_slice($rows, 0, 100);
        } catch (Exception) {
            return [];
        }
    }
}
