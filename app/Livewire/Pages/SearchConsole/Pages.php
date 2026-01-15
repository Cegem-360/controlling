<?php

declare(strict_types=1);

namespace App\Livewire\Pages\SearchConsole;

use App\Models\SearchPage;
use App\Models\Team;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Url;
use Livewire\Component;

final class Pages extends Component
{
    public ?Team $team = null;

    public string $dateRangeType = '28_days';

    /** @var array<int, array<string, mixed>> */
    public array $pages = [];

    // Search
    #[Url]
    public string $search = '';

    // Sorting
    public string $sortBy = 'date';

    public string $sortDir = 'desc';

    // Pagination
    public int $perPage = 10;

    public int $page = 1;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->dateRangeType = Session::get('search_console_pages_date_range', '28_days');
        $this->loadPagesData();
    }

    public function setDateRange(string $type): void
    {
        $this->dateRangeType = $type;
        session(['search_console_pages_date_range' => $type]);
        $this->loadPagesData();
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

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'desc';
        }
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
        ])->layout('components.layouts.dashboard');
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedPages(): array
    {
        $filtered = $this->filterData($this->pages, $this->search, ['page_url', 'device']);
        $sorted = $this->sortData($filtered, $this->sortBy, $this->sortDir);

        return $this->paginateData($sorted, $this->perPage, $this->page);
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
                'device' => ucfirst(mb_strtolower($item->device ?? __('Unknown'))),
                'impressions' => (int) $item->impressions,
                'clicks' => (int) $item->clicks,
                'ctr' => round((float) $item->ctr, 2),
                'position' => round((float) $item->position, 1),
            ])
            ->toArray();
    }
}
