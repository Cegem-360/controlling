<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('KPIs') }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('View and manage your Key Performance Indicators.') }}</p>
            </div>
        </div>
    </div>

    @if($team)
        <div class="flex flex-col gap-6">
            {{-- KPIs Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('All KPIs') }}</h3>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            wire:keydown.escape="$set('search', '')"
                            placeholder="{{ __('Search KPIs...') }}"
                            class="w-full sm:w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                @if(count($paginatedKpis['data']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                        <button wire:click="sort('name')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Name') }}
                                            @if($sortBy === 'name')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                        <button wire:click="sort('data_source')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Data Source') }}
                                            @if($sortBy === 'data_source')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                        <button wire:click="sort('category')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Category') }}
                                            @if($sortBy === 'category')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">
                                        <button wire:click="sort('target_value')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Target Value') }}
                                            @if($sortBy === 'target_value')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400">
                                        <button wire:click="sort('is_active')" class="flex items-center gap-1 mx-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Active') }}
                                            @if($sortBy === 'is_active')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                        <button wire:click="sort('target_date')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Target Date') }}
                                            @if($sortBy === 'target_date')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($paginatedKpis['data'] as $kpi)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $kpi['name'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                                {{ $kpi['data_source'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">
                                                {{ $kpi['category'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($kpi['target_value'] ?? 0) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($kpi['is_active'])
                                                <svg class="w-5 h-5 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 mx-auto text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $kpi['target_date'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <label class="text-sm text-gray-500 dark:text-gray-400">{{ __('Rows per page') }}:</label>
                            <select
                                wire:model.live="perPage"
                                class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-1 pl-2 pr-8 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer"
                            >
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Showing :from to :to of :total', [
                                    'from' => (($paginatedKpis['currentPage'] - 1) * $paginatedKpis['perPage']) + 1,
                                    'to' => min($paginatedKpis['currentPage'] * $paginatedKpis['perPage'], $paginatedKpis['total']),
                                    'total' => $paginatedKpis['total'],
                                ]) }}
                            </span>
                        </div>
                        @if($paginatedKpis['lastPage'] > 1)
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="$set('page', {{ max(1, $paginatedKpis['currentPage'] - 1) }})"
                                    @disabled($paginatedKpis['currentPage'] <= 1)
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    {{ __('Previous') }}
                                </button>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $paginatedKpis['currentPage'] }} / {{ $paginatedKpis['lastPage'] }}
                                </span>
                                <button
                                    wire:click="$set('page', {{ min($paginatedKpis['lastPage'], $paginatedKpis['currentPage'] + 1) }})"
                                    @disabled($paginatedKpis['currentPage'] >= $paginatedKpis['lastPage'])
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    {{ __('Next') }}
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="py-8 text-center text-gray-500 dark:text-gray-400">
                        @if($search)
                            {{ __('No KPIs match your search.') }}
                        @else
                            {{ __('No KPIs available.') }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Team Found') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('You need to create a team before viewing KPIs.') }}</p>
            <a href="{{ route('filament.admin.tenant.registration') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition cursor-pointer">
                {{ __('Create Team') }}
            </a>
        </div>
    @endif
</div>
