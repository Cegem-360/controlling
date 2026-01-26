<div x-data="dashboardCharts()" x-init="initCharts()">
    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white font-heading">
            {{ __('Welcome') }}, {{ auth()->user()->name }}!
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">
            {{ __('Here is your analytics overview for the last 30 days.') }}
        </p>
    </div>

    {{-- Integration Status Bar --}}
    @if ($teams->count() > 0)
        <div class="mb-8">
            @foreach ($teams as $team)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 {{ !$loop->last ? 'mb-4' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        {{-- Team info --}}
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-semibold shrink-0">
                                {{ strtoupper(substr($team->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $team->name }}</h3>
                                @if ($team->settings?->site_url)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $team->settings->site_url }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Integration status icons --}}
                        <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
                            {{-- Google Analytics --}}
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $team->settings?->property_id ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' }}" title="Google Analytics">
                                <div class="w-6 h-6 rounded-md bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24" fill="none">
                                        <path d="M22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22" stroke="currentColor" stroke-width="2" />
                                        <path d="M12 12L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                        <circle cx="12" cy="12" r="2" fill="currentColor" />
                                    </svg>
                                </div>
                                <span class="text-xs font-medium {{ $team->settings?->property_id ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
                                    Analytics
                                </span>
                                @if ($team->settings?->property_id)
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Search Console --}}
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $team->settings?->site_url ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' }}" title="Search Console">
                                <div class="w-6 h-6 rounded-md bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                                        <path d="M16 16L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <span class="text-xs font-medium {{ $team->settings?->site_url ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
                                    Search
                                </span>
                                @if ($team->settings?->site_url)
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Google Ads --}}
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $team->googleAdsSettings?->is_connected ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' }}" title="Google Ads">
                                <div class="w-6 h-6 rounded-md bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <span class="text-xs font-medium {{ $team->googleAdsSettings?->is_connected ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
                                    Ads
                                </span>
                                @if ($team->googleAdsSettings?->is_connected)
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Settings link --}}
                            <a href="{{ route('settings') }}" class="flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ __('Settings') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($currentTeam)
        {{-- Quick Stats Overview --}}
        <div class="grid grid-cols-2 gap-4 mb-8 lg:grid-cols-4">
            {{-- Sessions --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    @if (($analyticsStats['sessions']['change'] ?? 0) != 0)
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ ($analyticsStats['sessions']['change'] ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            @if (($analyticsStats['sessions']['change'] ?? 0) > 0)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            @endif
                            {{ abs($analyticsStats['sessions']['change'] ?? 0) }}%
                        </span>
                    @endif
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analyticsStats['sessions']['value'] ?? 0) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sessions') }}</p>
                </div>
            </div>

            {{-- Users --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    @if (($analyticsStats['users']['change'] ?? 0) != 0)
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ ($analyticsStats['users']['change'] ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            @if (($analyticsStats['users']['change'] ?? 0) > 0)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            @endif
                            {{ abs($analyticsStats['users']['change'] ?? 0) }}%
                        </span>
                    @endif
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analyticsStats['users']['value'] ?? 0) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Users') }}</p>
                </div>
            </div>

            {{-- Search Clicks --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-width="2" d="M16 16l5 5"/>
                        </svg>
                    </div>
                    @if (($searchConsoleStats['clicks']['change'] ?? 0) != 0)
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ ($searchConsoleStats['clicks']['change'] ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            @if (($searchConsoleStats['clicks']['change'] ?? 0) > 0)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            @endif
                            {{ abs($searchConsoleStats['clicks']['change'] ?? 0) }}%
                        </span>
                    @endif
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($searchConsoleStats['clicks']['value'] ?? 0) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Search Clicks') }}</p>
                </div>
            </div>

            {{-- Ad Spend --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @if (($googleAdsStats['cost']['change'] ?? 0) != 0)
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ ($googleAdsStats['cost']['change'] ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            @if (($googleAdsStats['cost']['change'] ?? 0) > 0)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            @endif
                            {{ abs($googleAdsStats['cost']['change'] ?? 0) }}%
                        </span>
                    @endif
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($googleAdsStats['cost']['value'] ?? 0) }} Ft</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Ad Spend') }}</p>
                </div>
            </div>
        </div>

        {{-- KPI Progress Section --}}
        @if (count($kpiStats) > 0)
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('KPI Progress') }}</h2>
                    <a href="{{ route('kpis.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                        {{ __('View all') }} &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($kpiStats as $kpi)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $kpi['name'] }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $kpi['category'] }}</p>
                                </div>
                                {{-- Circular Progress --}}
                                <div class="relative w-14 h-14 flex-shrink-0">
                                    <svg class="w-14 h-14 transform -rotate-90" viewBox="0 0 56 56">
                                        <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="none" class="text-gray-200 dark:text-gray-700"/>
                                        <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="none"
                                            class="{{ $kpi['progress'] >= 100 ? 'text-emerald-500' : ($kpi['progress'] >= 50 ? 'text-blue-500' : 'text-orange-500') }}"
                                            stroke-dasharray="{{ 2 * 3.14159 * 24 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 24 * (1 - min(100, $kpi['progress']) / 100) }}"
                                            stroke-linecap="round"/>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-900 dark:text-white">
                                        {{ round($kpi['progress']) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Current') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($kpi['current_value'], 0, ',', ' ') }}</span>
                                </div>
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Target') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($kpi['target_value'], 0, ',', ' ') }}</span>
                                </div>
                                @if ($kpi['days_remaining'] !== null)
                                    <div class="flex justify-between text-sm mt-1">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Days left') }}</span>
                                        <span class="font-medium {{ $kpi['days_remaining'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $kpi['days_remaining'] >= 0 ? $kpi['days_remaining'] : abs($kpi['days_remaining']) . ' ' . __('overdue') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
            {{-- Analytics Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Analytics') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sessions & Users (14 days)') }}</p>
                    </div>
                    <a href="{{ route('analytics.general') }}" class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                        {{ __('Details') }} &rarr;
                    </a>
                </div>
                <div class="h-48">
                    <canvas x-ref="analyticsChart"></canvas>
                </div>
                <div class="flex items-center justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Sessions') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Users') }}</span>
                    </div>
                </div>
            </div>

            {{-- Search Console Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Search Console') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Clicks & Impressions (14 days)') }}</p>
                    </div>
                    <a href="{{ route('search-console.general') }}" class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                        {{ __('Details') }} &rarr;
                    </a>
                </div>
                <div class="h-48">
                    <canvas x-ref="searchConsoleChart"></canvas>
                </div>
                <div class="flex items-center justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Clicks') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Impressions') }}</span>
                    </div>
                </div>
            </div>

            {{-- Google Ads Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Google Ads') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost & Conversions (14 days)') }}</p>
                    </div>
                    <a href="{{ route('google-ads.dashboard') }}" class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                        {{ __('Details') }} &rarr;
                    </a>
                </div>
                <div class="h-48">
                    <canvas x-ref="googleAdsChart"></canvas>
                </div>
                <div class="flex items-center justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Cost') }} (Ft)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Conversions') }}</span>
                    </div>
                </div>
            </div>

            {{-- Additional Stats Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Performance Metrics') }}</h3>
                <div class="space-y-4">
                    {{-- Bounce Rate --}}
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bounce Rate') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $analyticsStats['bounce_rate']['value'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $analyticsStats['bounce_rate']['value'] ?? 0) }}%"></div>
                        </div>
                    </div>

                    {{-- CTR --}}
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Search CTR') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $searchConsoleStats['ctr']['value'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, ($searchConsoleStats['ctr']['value'] ?? 0) * 10) }}%"></div>
                        </div>
                    </div>

                    {{-- Avg Position --}}
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Avg. Search Position') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $searchConsoleStats['position']['value'] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ max(0, 100 - (($searchConsoleStats['position']['value'] ?? 100) * 2)) }}%"></div>
                        </div>
                    </div>

                    {{-- Conversions --}}
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ad Conversions') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($googleAdsStats['conversions']['value'] ?? 0) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, ($googleAdsStats['conversions']['value'] ?? 0)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function dashboardCharts() {
    return {
        analyticsChart: null,
        searchConsoleChart: null,
        googleAdsChart: null,

        initCharts() {
            this.$nextTick(() => {
                this.createAnalyticsChart();
                this.createSearchConsoleChart();
                this.createGoogleAdsChart();
            });
        },

        createAnalyticsChart() {
            const ctx = this.$refs.analyticsChart;
            if (!ctx) return;

            const data = @json($analyticsChartData);
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            const textColor = isDark ? '#9CA3AF' : '#6B7280';

            this.analyticsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [
                        {
                            label: '{{ __("Sessions") }}',
                            data: data.map(d => d.sessions),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        },
                        {
                            label: '{{ __("Users") }}',
                            data: data.map(d => d.users),
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor, maxRotation: 0 }
                        },
                        y: {
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor },
                            beginAtZero: true
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        },

        createSearchConsoleChart() {
            const ctx = this.$refs.searchConsoleChart;
            if (!ctx) return;

            const data = @json($searchConsoleChartData);
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            const textColor = isDark ? '#9CA3AF' : '#6B7280';

            this.searchConsoleChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [
                        {
                            label: '{{ __("Clicks") }}',
                            data: data.map(d => d.clicks),
                            backgroundColor: '#8B5CF6',
                            borderRadius: 4,
                            yAxisID: 'y',
                        },
                        {
                            label: '{{ __("Impressions") }}',
                            data: data.map(d => d.impressions),
                            backgroundColor: '#F59E0B',
                            borderRadius: 4,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, maxRotation: 0 }
                        },
                        y: {
                            type: 'linear',
                            position: 'left',
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor },
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            grid: { display: false },
                            ticks: { color: textColor },
                            beginAtZero: true
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        },

        createGoogleAdsChart() {
            const ctx = this.$refs.googleAdsChart;
            if (!ctx) return;

            const data = @json($googleAdsChartData);
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            const textColor = isDark ? '#9CA3AF' : '#6B7280';

            this.googleAdsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [
                        {
                            label: '{{ __("Cost") }}',
                            data: data.map(d => d.cost),
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            yAxisID: 'y',
                        },
                        {
                            label: '{{ __("Conversions") }}',
                            data: data.map(d => d.conversions),
                            borderColor: '#22C55E',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor, maxRotation: 0 }
                        },
                        y: {
                            type: 'linear',
                            position: 'left',
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor },
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            grid: { display: false },
                            ticks: { color: textColor },
                            beginAtZero: true
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
    };
}
</script>
@endpush
