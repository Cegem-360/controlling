<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Search Queries') }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('View all search queries tracked in Google Search Console.') }}</p>
            </div>
            @if($team)
                <div>
                    {{ $this->setKpiGoalAction }}
                </div>
            @endif
        </div>
    </div>

    @if($team)
        <div class="flex flex-col gap-6">
            {{-- Date Range Filter --}}
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Period') }}:</span>
                <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden shadow-sm">
                    <button
                        wire:click="setDateRange('24_hours')"
                        class="{{ $dateRangeType === '24_hours' ? 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600 cursor-pointer"
                    >
                        {{ __('24 hours') }}
                    </button>
                    <button
                        wire:click="setDateRange('7_days')"
                        class="{{ $dateRangeType === '7_days' ? 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600 cursor-pointer"
                    >
                        {{ __('7 days') }}
                    </button>
                    <button
                        wire:click="setDateRange('28_days')"
                        class="{{ $dateRangeType === '28_days' ? 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600 cursor-pointer"
                    >
                        {{ __('28 days') }}
                    </button>
                    <button
                        wire:click="setDateRange('3_months')"
                        class="{{ $dateRangeType === '3_months' ? 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors cursor-pointer"
                    >
                        {{ __('3 months') }}
                    </button>
                </div>
            </div>

            {{-- Queries Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('All Queries') }}</h3>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            wire:keydown.escape="$set('search', '')"
                            placeholder="{{ __('Search queries...') }}"
                            class="w-full sm:w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                @if(count($paginatedQueries['data']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                        <button wire:click="sort('date')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Date') }}
                                            @if($sortBy === 'date')
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
                                        <button wire:click="sort('query')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Query') }}
                                            @if($sortBy === 'query')
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
                                        <button wire:click="sort('country')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Country') }}
                                            @if($sortBy === 'country')
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
                                        <button wire:click="sort('device')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Device') }}
                                            @if($sortBy === 'device')
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
                                        <button wire:click="sort('impressions')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Impressions') }}
                                            @if($sortBy === 'impressions')
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
                                        <button wire:click="sort('clicks')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Clicks') }}
                                            @if($sortBy === 'clicks')
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
                                        <button wire:click="sort('ctr')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('CTR') }}
                                            @if($sortBy === 'ctr')
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
                                        <button wire:click="sort('position')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Position') }}
                                            @if($sortBy === 'position')
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
                                @foreach($paginatedQueries['data'] as $query)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $query['date'] }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium truncate max-w-xs" title="{{ $query['query'] }}">{{ $query['query'] }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $query['country'] }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $query['device'] }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($query['impressions']) }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($query['clicks']) }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($query['ctr'], 2) }}%</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($query['position'], 1) }}</td>
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
                                    'from' => (($paginatedQueries['currentPage'] - 1) * $paginatedQueries['perPage']) + 1,
                                    'to' => min($paginatedQueries['currentPage'] * $paginatedQueries['perPage'], $paginatedQueries['total']),
                                    'total' => $paginatedQueries['total'],
                                ]) }}
                            </span>
                        </div>
                        @if($paginatedQueries['lastPage'] > 1)
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="$set('page', {{ max(1, $paginatedQueries['currentPage'] - 1) }})"
                                    @disabled($paginatedQueries['currentPage'] <= 1)
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    {{ __('Previous') }}
                                </button>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $paginatedQueries['currentPage'] }} / {{ $paginatedQueries['lastPage'] }}
                                </span>
                                <button
                                    wire:click="$set('page', {{ min($paginatedQueries['lastPage'], $paginatedQueries['currentPage'] + 1) }})"
                                    @disabled($paginatedQueries['currentPage'] >= $paginatedQueries['lastPage'])
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
                            {{ __('No queries match your search.') }}
                        @else
                            {{ __('No query data available.') }}
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
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('You need to create a team before viewing Search Queries.') }}</p>
            <a href="{{ route('filament.admin.tenant.registration') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition cursor-pointer">
                {{ __('Create Team') }}
            </a>
        </div>
    @endif

    <x-filament-actions::modals />
</div>
