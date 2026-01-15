<?php

declare(strict_types=1);

namespace App\Livewire\Pages\SearchConsole;

use App\Models\SearchPage;
use App\Models\SearchQuery;
use App\Models\Team;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Url;
use Livewire\Component;

final class GeneralStats extends Component
{
    public ?Team $team = null;

    public string $dateRangeType = '28_days';

    /** @var array<string, int|float> */
    public array $stats = [];

    /** @var array<int, array<string, mixed>> */
    public array $topQueries = [];

    /** @var array<int, array<string, mixed>> */
    public array $topPages = [];

    /** @var array<int, array<string, mixed>> */
    public array $deviceBreakdown = [];

    // Search
    #[Url]
    public string $queriesSearch = '';

    #[Url]
    public string $pagesSearch = '';

    // Sorting
    public string $queriesSortBy = 'clicks';

    public string $queriesSortDir = 'desc';

    public string $pagesSortBy = 'clicks';

    public string $pagesSortDir = 'desc';

    // Pagination
    public int $queriesPerPage = 10;

    public int $pagesPerPage = 10;

    public int $queriesPage = 1;

    public int $pagesPage = 1;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->dateRangeType = Session::get('search_console_date_range', '28_days');
        $this->loadSearchConsoleData();
    }

    public function setDateRange(string $type): void
    {
        $this->dateRangeType = $type;
        session(['search_console_date_range' => $type]);
        $this->loadSearchConsoleData();
    }

    public function getStartDate(): CarbonInterface
    {
        return match ($this->dateRangeType) {
            '24_hours' => now()->subHours(24),
            '7_days' => now()->subDays(7),
            '28_days' => now()->subDays(28),
            '3_months' => now()->subMonths(3),
            default => now()->subDays(28),
        };
    }

    public function sortQueries(string $column): void
    {
        if ($this->queriesSortBy === $column) {
            $this->queriesSortDir = $this->queriesSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->queriesSortBy = $column;
            $this->queriesSortDir = 'desc';
        }
        $this->queriesPage = 1;
    }

    public function sortPages(string $column): void
    {
        if ($this->pagesSortBy === $column) {
            $this->pagesSortDir = $this->pagesSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->pagesSortBy = $column;
            $this->pagesSortDir = 'desc';
        }
        $this->pagesPage = 1;
    }

    public function updatedQueriesSearch(): void
    {
        $this->queriesPage = 1;
    }

    public function updatedPagesSearch(): void
    {
        $this->pagesPage = 1;
    }

    public function updatedQueriesPerPage(): void
    {
        $this->queriesPage = 1;
    }

    public function updatedPagesPerPage(): void
    {
        $this->pagesPage = 1;
    }

    public function render(): View
    {
        return view('livewire.pages.search-console.general-stats', [
            'paginatedQueries' => $this->getPaginatedQueries(),
            'paginatedPages' => $this->getPaginatedPages(),
        ])->layout('components.layouts.dashboard');
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedQueries(): array
    {
        $filtered = $this->filterData($this->topQueries, $this->queriesSearch, ['query']);
        $sorted = $this->sortData($filtered, $this->queriesSortBy, $this->queriesSortDir);

        return $this->paginateData($sorted, $this->queriesPerPage, $this->queriesPage);
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedPages(): array
    {
        $filtered = $this->filterData($this->topPages, $this->pagesSearch, ['page_url']);
        $sorted = $this->sortData($filtered, $this->pagesSortBy, $this->pagesSortDir);

        return $this->paginateData($sorted, $this->pagesPerPage, $this->pagesPage);
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

    private function loadSearchConsoleData(): void
    {
        $startDate = $this->getStartDate();

        // Load general stats
        $this->stats = [
            'total_impressions' => SearchQuery::query()->where('date', '>=', $startDate)->sum('impressions'),
            'total_clicks' => SearchQuery::query()->where('date', '>=', $startDate)->sum('clicks'),
            'avg_ctr' => SearchQuery::query()->where('date', '>=', $startDate)->avg('ctr') ?? 0,
            'avg_position' => SearchQuery::query()->where('date', '>=', $startDate)->avg('position') ?? 0,
        ];

        // Load top queries (increased limit for pagination)
        $this->topQueries = SearchQuery::query()
            ->select('query', DB::raw('SUM(impressions) as total_impressions'), DB::raw('SUM(clicks) as total_clicks'), DB::raw('AVG(ctr) as avg_ctr'), DB::raw('AVG(position) as avg_position'))
            ->where('date', '>=', $startDate)
            ->groupBy('query')
            ->orderByDesc('total_clicks')
            ->limit(100)
            ->get()
            ->map(fn ($item): array => [
                'query' => $item->query,
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'ctr' => round((float) $item->avg_ctr, 2),
                'position' => round((float) $item->avg_position, 2),
            ])
            ->toArray();

        // Load top pages (increased limit for pagination)
        $this->topPages = SearchPage::query()
            ->select('page_url', DB::raw('SUM(impressions) as total_impressions'), DB::raw('SUM(clicks) as total_clicks'), DB::raw('AVG(ctr) as avg_ctr'), DB::raw('AVG(position) as avg_position'))
            ->where('date', '>=', $startDate)
            ->groupBy('page_url')
            ->orderByDesc('total_clicks')
            ->limit(100)
            ->get()
            ->map(fn ($item): array => [
                'page_url' => $item->page_url,
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
                'ctr' => round((float) $item->avg_ctr, 2),
                'position' => round((float) $item->avg_position, 2),
            ])
            ->toArray();

        // Load device breakdown
        $this->deviceBreakdown = SearchQuery::query()
            ->select('device', DB::raw('SUM(impressions) as total_impressions'), DB::raw('SUM(clicks) as total_clicks'))
            ->where('date', '>=', $startDate)
            ->groupBy('device')
            ->get()
            ->map(fn ($item): array => [
                'device' => $item->device,
                'impressions' => (int) $item->total_impressions,
                'clicks' => (int) $item->total_clicks,
            ])
            ->toArray();
    }
}
