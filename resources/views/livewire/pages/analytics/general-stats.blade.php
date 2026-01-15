<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('General Analytics Dashboard') }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('Overview of your Google Analytics data.') }}</p>
            </div>
        </div>
    </div>

    @if($team)
        {{-- Stats Cards --}}
        @if(count($stats) > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                {{-- Active Users --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Active Users') }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active_users'] ?? 0) }}</div>
                    <div class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">{{ __('Last 30 days') }}</div>
                </div>

                {{-- New Users --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('New Users') }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['new_users'] ?? 0) }}</div>
                    <div class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">{{ __('Last 30 days') }}</div>
                </div>

                {{-- Avg Session Duration --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Avg Session Duration') }}</div>
                    @php
                        $duration = $stats['avg_session_duration'] ?? 0;
                        $minutes = floor($duration / 60);
                        $seconds = round($duration % 60);
                    @endphp
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $minutes }}m {{ $seconds }}s</div>
                    <div class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">{{ __('Average session') }}</div>
                </div>

                {{-- Event Count --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Event Count') }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['event_count'] ?? 0) }}</div>
                    <div class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">{{ __('Total events') }}</div>
                </div>
            </div>
        @endif

        {{-- Top Pages Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Top Pages') }}</h2>
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="topPagesSearch"
                        wire:keydown.escape="$set('topPagesSearch', '')"
                        placeholder="{{ __('Search pages...') }}"
                        class="w-full sm:w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            @if(count($paginatedTopPages['data']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <button wire:click="sortTopPages('page_title')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                        {{ __('Page Title & Path') }}
                                        @if($topPagesSortBy === 'page_title')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($topPagesSortDir === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <button wire:click="sortTopPages('views')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                        {{ __('Views') }}
                                        @if($topPagesSortBy === 'views')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($topPagesSortDir === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <button wire:click="sortTopPages('active_users')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                        {{ __('Active Users') }}
                                        @if($topPagesSortBy === 'active_users')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($topPagesSortDir === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <button wire:click="sortTopPages('event_count')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                        {{ __('Events') }}
                                        @if($topPagesSortBy === 'event_count')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($topPagesSortDir === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <button wire:click="sortTopPages('bounce_rate')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                        {{ __('Bounce Rate') }}
                                        @if($topPagesSortBy === 'bounce_rate')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($topPagesSortDir === 'asc')
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
                            @foreach($paginatedTopPages['data'] as $page)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $page['page_title'] }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-md">{{ $page['page_path'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                        {{ number_format($page['views']) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                        {{ number_format($page['active_users']) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                        {{ number_format($page['event_count']) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                        {{ $page['bounce_rate'] }}%
                                    </td>
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
                            wire:model.live="topPagesPerPage"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-1 pl-2 pr-8 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Showing :from to :to of :total', [
                                'from' => (($paginatedTopPages['currentPage'] - 1) * $paginatedTopPages['perPage']) + 1,
                                'to' => min($paginatedTopPages['currentPage'] * $paginatedTopPages['perPage'], $paginatedTopPages['total']),
                                'total' => $paginatedTopPages['total'],
                            ]) }}
                        </span>
                    </div>
                    @if($paginatedTopPages['lastPage'] > 1)
                        <div class="flex items-center gap-2">
                            <button
                                wire:click="$set('topPagesPage', {{ max(1, $paginatedTopPages['currentPage'] - 1) }})"
                                @disabled($paginatedTopPages['currentPage'] <= 1)
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            >
                                {{ __('Previous') }}
                            </button>
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $paginatedTopPages['currentPage'] }} / {{ $paginatedTopPages['lastPage'] }}
                            </span>
                            <button
                                wire:click="$set('topPagesPage', {{ min($paginatedTopPages['lastPage'], $paginatedTopPages['currentPage'] + 1) }})"
                                @disabled($paginatedTopPages['currentPage'] >= $paginatedTopPages['lastPage'])
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            >
                                {{ __('Next') }}
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    @if($topPagesSearch)
                        {{ __('No pages match your search.') }}
                    @else
                        {{ __('No page data available') }}
                    @endif
                </div>
            @endif
        </div>

        {{-- Bottom Row: User Sources and Session Sources --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- User Sources Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Active Users by Source') }}</h2>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="userSourcesSearch"
                            wire:keydown.escape="$set('userSourcesSearch', '')"
                            placeholder="{{ __('Search...') }}"
                            class="w-full sm:w-48 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                @if(count($paginatedUserSources['data']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        <button wire:click="sortUserSources('source')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Source / Medium') }}
                                            @if($userSourcesSortBy === 'source')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($userSourcesSortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        <button wire:click="sortUserSources('users')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Users') }}
                                            @if($userSourcesSortBy === 'users')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($userSourcesSortDir === 'asc')
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
                                @foreach($paginatedUserSources['data'] as $source)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $source['source'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $source['medium'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($source['users']) }}
                                        </td>
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
                                wire:model.live="userSourcesPerPage"
                                class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-1 pl-2 pr-8 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer"
                            >
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Showing :from to :to of :total', [
                                    'from' => (($paginatedUserSources['currentPage'] - 1) * $paginatedUserSources['perPage']) + 1,
                                    'to' => min($paginatedUserSources['currentPage'] * $paginatedUserSources['perPage'], $paginatedUserSources['total']),
                                    'total' => $paginatedUserSources['total'],
                                ]) }}
                            </span>
                        </div>
                        @if($paginatedUserSources['lastPage'] > 1)
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="$set('userSourcesPage', {{ max(1, $paginatedUserSources['currentPage'] - 1) }})"
                                    @disabled($paginatedUserSources['currentPage'] <= 1)
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    {{ __('Previous') }}
                                </button>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $paginatedUserSources['currentPage'] }} / {{ $paginatedUserSources['lastPage'] }}
                                </span>
                                <button
                                    wire:click="$set('userSourcesPage', {{ min($paginatedUserSources['lastPage'], $paginatedUserSources['currentPage'] + 1) }})"
                                    @disabled($paginatedUserSources['currentPage'] >= $paginatedUserSources['lastPage'])
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    {{ __('Next') }}
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        @if($userSourcesSearch)
                            {{ __('No sources match your search.') }}
                        @else
                            {{ __('No user source data available') }}
                        @endif
                    </div>
                @endif
            </div>

            {{-- Session Sources Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Sessions by Source') }}</h2>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="sessionSourcesSearch"
                            wire:keydown.escape="$set('sessionSourcesSearch', '')"
                            placeholder="{{ __('Search...') }}"
                            class="w-full sm:w-48 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                @if(count($paginatedSessionSources['data']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        <button wire:click="sortSessionSources('source')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Source / Medium') }}
                                            @if($sessionSourcesSortBy === 'source')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sessionSourcesSortDir === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    @endif
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        <button wire:click="sortSessionSources('sessions')" class="flex items-center gap-1 ml-auto hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                            {{ __('Sessions') }}
                                            @if($sessionSourcesSortBy === 'sessions')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($sessionSourcesSortDir === 'asc')
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
                                @foreach($paginatedSessionSources['data'] as $source)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $source['source'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $source['medium'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($source['sessions']) }}
                                        </td>
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
                                wire:model.live="sessionSourcesPerPage"
                                class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-1 pl-2 pr-8 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer"
                            >
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Showing :from to :to of :total', [
                                    'from' => (($paginatedSessionSources['currentPage'] - 1) * $paginatedSessionSources['perPage']) + 1,
                                    'to' => min($paginatedSessionSources['currentPage'] * $paginatedSessionSources['perPage'], $paginatedSessionSources['total']),
                                    'total' => $paginatedSessionSources['total'],
                                ]) }}
                            </span>
                        </div>
                        @if($paginatedSessionSources['lastPage'] > 1)
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="$set('sessionSourcesPage', {{ max(1, $paginatedSessionSources['currentPage'] - 1) }})"
                                    @disabled($paginatedSessionSources['currentPage'] <= 1)
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    {{ __('Previous') }}
                                </button>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $paginatedSessionSources['currentPage'] }} / {{ $paginatedSessionSources['lastPage'] }}
                                </span>
                                <button
                                    wire:click="$set('sessionSourcesPage', {{ min($paginatedSessionSources['lastPage'], $paginatedSessionSources['currentPage'] + 1) }})"
                                    @disabled($paginatedSessionSources['currentPage'] >= $paginatedSessionSources['lastPage'])
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    {{ __('Next') }}
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        @if($sessionSourcesSearch)
                            {{ __('No sources match your search.') }}
                        @else
                            {{ __('No session source data available') }}
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
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('You need to create a team before viewing analytics.') }}</p>
            <a href="{{ route('filament.admin.tenant.registration') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition cursor-pointer">
                {{ __('Create Team') }}
            </a>
        </div>
    @endif
</div>
