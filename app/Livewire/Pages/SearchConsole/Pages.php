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
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class Pages extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithDataTable;
    use WithSearchConsoleDateRange;

    private const SESSION_KEY = 'search_console_pages_date_range';

    private const SEARCH_FIELDS = ['page_url', 'device'];

    public ?Team $team = null;

    /** @var array<int, array<string, mixed>> */
    public array $pages = [];

    /** @var array<int, array<string, mixed>> */
    public array $topPages = [];

    /** @var array<int, array<string, mixed>> */
    public array $topQueries = [];

    #[Url]
    public string $search = '';

    public string $sortBy = 'date';

    public string $sortDir = 'desc';

    public int $perPage = 10;

    public int $page = 1;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->dateRangeType = Session::get(self::SESSION_KEY, '28_days');
        $this->loadPagesData();
    }

    public function setDateRange(string $type): void
    {
        $this->setDateRangeWithSession($type, self::SESSION_KEY);
        $this->loadPagesData();
    }

    public function sort(string $column): void
    {
        $result = $this->toggleSort($column, $this->sortBy, $this->sortDir);
        $this->sortBy = $result['sortBy'];
        $this->sortDir = $result['sortDir'];
        $this->page = 1;
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedPerPage(): void
    {
        $this->page = 1;
    }

    public function render(): View
    {
        return view('livewire.pages.search-console.pages', [
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
    private function getPaginatedPages(): array
    {
        $filtered = $this->filterData($this->pages, $this->search, self::SEARCH_FIELDS);
        $sorted = $this->sortData($filtered, $this->sortBy, $this->sortDir);

        return $this->paginateData($sorted, $this->perPage, $this->page);
    }

    private function loadPagesData(): void
    {
        $startDate = $this->getStartDate();

        $this->pages = SearchPage::query()
            ->where('date', '>=', $startDate)
            ->orderByDesc('date')
            ->limit(500)
            ->get()
            ->map(fn ($item): array => [
                'date' => $item->date->format('Y-m-d'),
                'page_url' => $item->page_url,
                'device' => Str::ucfirst(Str::lower($item->device ?? __('Unknown'))),
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'ctr' => round((float) $item->ctr, 2),
                'position' => round((float) $item->position, 1),
            ])
            ->toArray();

        $this->loadKpiData($startDate);
    }

    private function loadKpiData(CarbonInterface $startDate): void
    {
        $this->topPages = SearchPage::query()
            ->select(
                'page_url',
                DB::raw('SUM(clicks) as clicks'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('page_url')
            ->orderByDesc('clicks')
            ->limit(100)
            ->get()
            ->map(fn ($item): array => [
                'page_url' => $item->page_url,
                'clicks' => (int) $item->clicks,
            ])
            ->toArray();

        $this->topQueries = SearchQuery::query()
            ->select(
                'query',
                DB::raw('SUM(clicks) as clicks'),
            )
            ->where('date', '>=', $startDate)
            ->groupBy('query')
            ->orderByDesc('clicks')
            ->limit(100)
            ->get()
            ->map(fn ($item): array => [
                'query' => $item->query,
                'clicks' => (int) $item->clicks,
            ])
            ->toArray();
    }
}
