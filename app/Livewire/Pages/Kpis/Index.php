<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Kpis;

use App\Models\Kpi;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class Index extends Component
{
    public ?Team $team = null;

    /** @var array<int, array<string, mixed>> */
    public array $kpis = [];

    // Search
    #[Url]
    public string $search = '';

    // Sorting
    public string $sortBy = 'name';

    public string $sortDir = 'asc';

    // Pagination
    public int $perPage = 10;

    public int $page = 1;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->loadKpisData();
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
        return view('livewire.pages.kpis.index', [
            'paginatedKpis' => $this->getPaginatedKpis(),
        ]);
    }

    /**
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    private function getPaginatedKpis(): array
    {
        $filtered = $this->filterData($this->kpis, $this->search, ['name', 'data_source', 'category']);
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

    private function loadKpisData(): void
    {
        if (! $this->team) {
            return;
        }

        $this->kpis = Kpi::query()
            ->where('team_id', $this->team->id)
            ->orderBy('name')
            ->get()
            ->map(fn ($item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'data_source' => $item->data_source?->value ?? '',
                'category' => $item->category?->value ?? '',
                'target_value' => $item->target_value,
                'is_active' => $item->is_active,
                'target_date' => $item->target_date?->format('Y-m-d'),
                'created_at' => $item->created_at?->format('Y-m-d H:i'),
            ])
            ->toArray();
    }
}
