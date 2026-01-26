<?php

declare(strict_types=1);

namespace App\Livewire\Pages\SearchConsole;

use App\Filament\Pages\Actions\SetSearchConsoleKpiGoalAction;
use App\Livewire\Concerns\WithDataTable;
use App\Livewire\Concerns\WithSearchConsoleDateRange;
use App\Models\SearchPage;
use App\Models\SearchQuery;
use App\Models\Team;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class GeneralStats extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithDataTable;
    use WithSearchConsoleDateRange;

    private const string SESSION_KEY = 'search_console_date_range';

    public ?Team $team = null;

    /** @var array<string, int|float> */
    public array $stats = [];

    /** @var array<int, array<string, mixed>> */
    public array $topQueries = [];

    /** @var array<int, array<string, mixed>> */
    public array $topPages = [];

    /** @var array<int, array<string, mixed>> */
    public array $deviceBreakdown = [];

    #[Url]
    public string $queriesSearch = '';

    #[Url]
    public string $pagesSearch = '';

    public string $queriesSortBy = 'clicks';

    public string $queriesSortDir = 'desc';

    public string $pagesSortBy = 'clicks';

    public string $pagesSortDir = 'desc';

    public int $queriesPerPage = 10;

    public int $pagesPerPage = 10;

    public int $queriesPage = 1;

    public int $pagesPage = 1;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->dateRangeType = Session::get(self::SESSION_KEY, '28_days');
        $this->loadSearchConsoleData();
    }

    public function setDateRange(string $type): void
    {
        $this->setDateRangeWithSession($type, self::SESSION_KEY);
        $this->loadSearchConsoleData();
    }

    public function sortQueries(string $column): void
    {
        $result = $this->toggleSort($column, $this->queriesSortBy, $this->queriesSortDir);
        $this->queriesSortBy = $result['sortBy'];
        $this->queriesSortDir = $result['sortDir'];
        $this->queriesPage = 1;
    }

    public function sortPages(string $column): void
    {
        $result = $this->toggleSort($column, $this->pagesSortBy, $this->pagesSortDir);
        $this->pagesSortBy = $result['sortBy'];
        $this->pagesSortDir = $result['sortDir'];
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
        ]);
    }

    public function setKpiGoalAction(): Action
    {
        return SetSearchConsoleKpiGoalAction::make(
            fn (): array => $this->topPages,
            fn (): array => $this->topQueries,
        )->icon('heroicon-o-chart-bar');
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

    private function loadSearchConsoleData(): void
    {
        $startDate = $this->getStartDate();

        $this->loadStats($startDate);
        $this->loadTopQueries($startDate);
        $this->loadTopPages($startDate);
        $this->loadDeviceBreakdown($startDate);
    }

    private function loadStats(CarbonInterface $startDate): void
    {
        $this->stats = [
            'total_impressions' => SearchQuery::query()->where('date', '>=', $startDate)->sum('impressions'),
            'total_clicks' => SearchQuery::query()->where('date', '>=', $startDate)->sum('clicks'),
            'avg_ctr' => SearchQuery::query()->where('date', '>=', $startDate)->avg('ctr') ?? 0,
            'avg_position' => SearchQuery::query()->where('date', '>=', $startDate)->avg('position') ?? 0,
        ];
    }

    private function loadTopQueries(CarbonInterface $startDate): void
    {
        $this->topQueries = SearchQuery::query()
            ->select(
                'query',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('AVG(ctr) as avg_ctr'),
                DB::raw('AVG(position) as avg_position'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('query')
            ->orderByDesc('total_clicks')
            ->limit(100)
            ->get()
            ->map(fn (object $item): array => $this->mapSearchConsoleStats($item, ['query' => $item->query]))
            ->toArray();
    }

    private function loadTopPages(CarbonInterface $startDate): void
    {
        $this->topPages = SearchPage::query()
            ->select(
                'page_url',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('AVG(ctr) as avg_ctr'),
                DB::raw('AVG(position) as avg_position'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('page_url')
            ->orderByDesc('total_clicks')
            ->limit(100)
            ->get()
            ->map(fn (object $item): array => $this->mapSearchConsoleStats($item, ['page_url' => $item->page_url]))
            ->toArray();
    }

    private function loadDeviceBreakdown(CarbonInterface $startDate): void
    {
        $this->deviceBreakdown = SearchQuery::query()
            ->select(
                'device',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
            )
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

    /**
     * Map aggregated Search Console query results to standardized stats array.
     *
     * @param  object  $item  Database query result with total_impressions, total_clicks, avg_ctr, avg_position
     * @param  array<string, mixed>  $extraFields  Additional fields to include in the result
     * @return array<string, mixed>
     */
    private function mapSearchConsoleStats(object $item, array $extraFields = []): array
    {
        return [
            ...$extraFields,
            'impressions' => (int) $item->total_impressions,
            'clicks' => (int) $item->total_clicks,
            'ctr' => round((float) $item->avg_ctr, 2),
            'position' => round((float) $item->avg_position, 2),
        ];
    }
}
