<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

/**
 * Provides common data table functionality for filtering, sorting, and pagination.
 */
trait WithDataTable
{
    /**
     * Filter data by searching specified fields.
     *
     * @param  array<int, array<string, mixed>>  $data
     * @param  array<string>  $searchFields
     * @return array<int, array<string, mixed>>
     */
    protected function filterData(array $data, string $search, array $searchFields): array
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
     * Sort data by column and direction.
     *
     * @param  array<int, array<string, mixed>>  $data
     * @return array<int, array<string, mixed>>
     */
    protected function sortData(array $data, string $sortBy, string $sortDir): array
    {
        usort($data, function (array $a, array $b) use ($sortBy, $sortDir): int {
            $aVal = $a[$sortBy] ?? 0;
            $bVal = $b[$sortBy] ?? 0;

            $result = is_string($aVal) ? strcmp($aVal, (string) $bVal) : $aVal <=> $bVal;

            return $sortDir === 'asc' ? $result : -$result;
        });

        return $data;
    }

    /**
     * Paginate data array.
     *
     * @param  array<int, array<string, mixed>>  $data
     * @return array{data: array<int, array<string, mixed>>, total: int, perPage: int, currentPage: int, lastPage: int}
     */
    protected function paginateData(array $data, int $perPage, int $currentPage): array
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

    /**
     * Toggle sort direction for a column, or set a new column with default desc direction.
     *
     * @return array{sortBy: string, sortDir: string}
     */
    protected function toggleSort(string $column, string $currentSortBy, string $currentSortDir): array
    {
        if ($currentSortBy === $column) {
            return [
                'sortBy' => $column,
                'sortDir' => $currentSortDir === 'asc' ? 'desc' : 'asc',
            ];
        }

        return [
            'sortBy' => $column,
            'sortDir' => 'desc',
        ];
    }
}
