<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Analytics;

use App\Filament\Pages\Actions\SetAnalyticsKpiGoalAction;
use App\Models\GlobalSetting;
use App\Models\Team;
use App\Services\GoogleClientFactory;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\DateRange;
use Google\Service\AnalyticsData\Dimension;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\Row;
use Google\Service\AnalyticsData\RunReportRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
final class GeneralStats extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithPagination;

    public ?Team $team = null;

    /** @var array<int, array<string, mixed>> */
    public array $topPages = [];

    /** @var array<int, array<string, mixed>> */
    public array $userSources = [];

    /** @var array<int, array<string, mixed>> */
    public array $sessionSources = [];

    /** @var array<string, mixed> */
    public array $stats = [];

    // Search
    #[Url]
    public string $topPagesSearch = '';

    #[Url]
    public string $userSourcesSearch = '';

    #[Url]
    public string $sessionSourcesSearch = '';

    // Sorting
    public string $topPagesSortBy = 'views';

    public string $topPagesSortDir = 'desc';

    public string $userSourcesSortBy = 'users';

    public string $userSourcesSortDir = 'desc';

    public string $sessionSourcesSortBy = 'sessions';

    public string $sessionSourcesSortDir = 'desc';

    // Pagination
    public int $topPagesPerPage = 10;

    public int $userSourcesPerPage = 10;

    public int $sessionSourcesPerPage = 10;

    public int $topPagesPage = 1;

    public int $userSourcesPage = 1;

    public int $sessionSourcesPage = 1;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->loadAnalyticsData();
    }

    public function render(): View
    {
        return view('livewire.pages.analytics.general-stats', [
            'paginatedTopPages' => $this->getPaginatedTopPages(),
            'paginatedUserSources' => $this->getPaginatedUserSources(),
            'paginatedSessionSources' => $this->getPaginatedSessionSources(),
        ]);
    }

    public function setKpiGoalAction(): Action
    {
        return SetAnalyticsKpiGoalAction::make(fn (): array => $this->topPages)
            ->icon('heroicon-o-chart-bar');
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

    public function sortSessionSources(string $column): void
    {
        if ($this->sessionSourcesSortBy === $column) {
            $this->sessionSourcesSortDir = $this->sessionSourcesSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sessionSourcesSortBy = $column;
            $this->sessionSourcesSortDir = 'desc';
        }
        $this->sessionSourcesPage = 1;
    }

    public function updatedTopPagesSearch(): void
    {
        $this->topPagesPage = 1;
    }

    public function updatedUserSourcesSearch(): void
    {
        $this->userSourcesPage = 1;
    }

    public function updatedSessionSourcesSearch(): void
    {
        $this->sessionSourcesPage = 1;
    }

    public function updatedTopPagesPerPage(): void
    {
        $this->topPagesPage = 1;
    }

    public function updatedUserSourcesPerPage(): void
    {
        $this->userSourcesPage = 1;
    }

    public function updatedSessionSourcesPerPage(): void
    {
        $this->sessionSourcesPage = 1;
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedTopPages(): array
    {
        $filtered = $this->filterData($this->topPages, $this->topPagesSearch, ['page_title', 'page_path']);
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
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedSessionSources(): array
    {
        $filtered = $this->filterData($this->sessionSources, $this->sessionSourcesSearch, ['source', 'medium']);
        $sorted = $this->sortData($filtered, $this->sessionSourcesSortBy, $this->sessionSourcesSortDir);

        return $this->paginateData($sorted, $this->sessionSourcesPerPage, $this->sessionSourcesPage);
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
        if (! $this->team?->settings?->property_id) {
            return;
        }

        $this->loadTopPages();
        $this->loadUserSources();
        $this->loadSessionSources();
        $this->loadStats();
    }

    private function loadTopPages(): void
    {
        $this->topPages = $this->runReport(
            dimensions: ['pageTitle', 'pagePath'],
            metrics: ['screenPageViews', 'activeUsers', 'eventCount', 'bounceRate'],
            extractRow: fn (Row $row): array => [
                'page_title' => $row->getDimensionValues()[0]->getValue(),
                'page_path' => $row->getDimensionValues()[1]->getValue(),
                'views' => (int) $row->getMetricValues()[0]->getValue(),
                'active_users' => (int) $row->getMetricValues()[1]->getValue(),
                'event_count' => (int) $row->getMetricValues()[2]->getValue(),
                'bounce_rate' => round((float) $row->getMetricValues()[3]->getValue() * 100, 1),
            ],
            sortBy: 'views',
            limit: 100,
        );
    }

    private function loadUserSources(): void
    {
        $this->userSources = $this->runReport(
            dimensions: ['firstUserSource', 'firstUserMedium'],
            metrics: ['activeUsers'],
            extractRow: fn (Row $row): array => [
                'source' => $row->getDimensionValues()[0]->getValue(),
                'medium' => $row->getDimensionValues()[1]->getValue(),
                'users' => (int) $row->getMetricValues()[0]->getValue(),
            ],
            sortBy: 'users',
        );
    }

    private function loadSessionSources(): void
    {
        $this->sessionSources = $this->runReport(
            dimensions: ['sessionSource', 'sessionMedium'],
            metrics: ['sessions'],
            extractRow: fn (Row $row): array => [
                'source' => $row->getDimensionValues()[0]->getValue(),
                'medium' => $row->getDimensionValues()[1]->getValue(),
                'sessions' => (int) $row->getMetricValues()[0]->getValue(),
            ],
            sortBy: 'sessions',
        );
    }

    private function loadStats(): void
    {
        $statsData = $this->runReport(
            dimensions: [],
            metrics: ['activeUsers', 'newUsers', 'averageSessionDuration', 'eventCount'],
            extractRow: fn (Row $row): array => [
                'active_users' => (int) $row->getMetricValues()[0]->getValue(),
                'new_users' => (int) $row->getMetricValues()[1]->getValue(),
                'avg_session_duration' => (float) $row->getMetricValues()[2]->getValue(),
                'event_count' => (int) $row->getMetricValues()[3]->getValue(),
            ],
            sortBy: 'active_users',
            limit: 1,
        );

        $this->stats = $statsData[0] ?? [
            'active_users' => 0,
            'new_users' => 0,
            'avg_session_duration' => 0,
            'event_count' => 0,
        ];
    }

    /**
     * @param  array<string>  $dimensions
     * @param  array<string>  $metrics
     * @param  callable(Row): array<string, mixed>  $extractRow
     * @return array<int, array<string, mixed>>
     */
    private function runReport(array $dimensions, array $metrics, callable $extractRow, string $sortBy, int $limit = 50): array
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
            $dateRange->setStartDate('30daysAgo');
            $dateRange->setEndDate('today');

            $request = new RunReportRequest();
            $request->setDateRanges([$dateRange]);

            if (count($dimensions) > 0) {
                $request->setDimensions(array_map(function (string $name): Dimension {
                    $dimension = new Dimension();
                    $dimension->setName($name);

                    return $dimension;
                }, $dimensions));
            }

            $request->setMetrics(array_map(function (string $name): Metric {
                $metric = new Metric();
                $metric->setName($name);

                return $metric;
            }, $metrics));

            $request->setLimit((string) $limit);

            $response = $service->properties->runReport(
                "properties/{$settings->property_id}",
                $request,
            );

            $rows = array_map($extractRow, $response->getRows() ?? []);

            if ($sortBy && count($rows) > 0 && isset($rows[0][$sortBy])) {
                usort($rows, fn (array $a, array $b): int => $b[$sortBy] <=> $a[$sortBy]);
            }

            return $rows;
        } catch (Exception) {
            return [];
        }
    }
}
