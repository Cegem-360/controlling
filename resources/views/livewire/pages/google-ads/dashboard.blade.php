<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Google Ads Dashboard') }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('Overview of your Google Ads campaigns and performance.') }}</p>
            </div>
        </div>
    </div>

    @if($team)
        <div class="flex flex-col gap-6">
            {{-- Date Range Filter --}}
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Period') }}:</span>
                <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden shadow-sm">
                    <button
                        wire:click="setDateRange('7_days')"
                        class="{{ $dateRangeType === '7_days' ? 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                    >
                        {{ __('7 days') }}
                    </button>
                    <button
                        wire:click="setDateRange('28_days')"
                        class="{{ $dateRangeType === '28_days' ? 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                    >
                        {{ __('28 days') }}
                    </button>
                    <button
                        wire:click="setDateRange('3_months')"
                        class="{{ $dateRangeType === '3_months' ? 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors"
                    >
                        {{ __('3 months') }}
                    </button>
                </div>
            </div>

            {{-- Stats Overview --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_impressions'] ?? 0) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_clicks'] ?? 0) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_cost'] ?? 0, 0, ',', ' ') }} Ft</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_conversions'] ?? 0, 0) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Avg. CTR') }}</div>
                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_ctr'] ?? 0, 2) }}%</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Avg. CPC') }}</div>
                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_cpc'] ?? 0, 2, ',', ' ') }} Ft</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost / Conversion') }}</div>
                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['cost_per_conversion'] ?? 0, 2, ',', ' ') }} Ft</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Conversion Rate') }}</div>
                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_conversion_rate'] ?? 0, 2) }}%</div>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div x-data="{ activeTab: 'campaigns' }" class="flex flex-col gap-6">
                {{-- Tab Headers --}}
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-4 overflow-x-auto" aria-label="Tabs">
                        <button @click="activeTab = 'campaigns'" :class="activeTab === 'campaigns' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-500 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Campaigns') }}</button>
                        <button @click="activeTab = 'adgroups'" :class="activeTab === 'adgroups' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-500 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Ad Groups') }}</button>
                        <button @click="activeTab = 'hourly'" :class="activeTab === 'hourly' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-500 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('By Hour') }}</button>
                        <button @click="activeTab = 'devices'" :class="activeTab === 'devices' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-500 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Devices') }}</button>
                        <button @click="activeTab = 'demographics'" :class="activeTab === 'demographics' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-500 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Demographics') }}</button>
                        <button @click="activeTab = 'geographic'" :class="activeTab === 'geographic' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-500 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Geographic') }}</button>
                    </nav>
                </div>

                {{-- Tab Contents --}}
                <div>
                    {{-- Campaigns Tab --}}
                    <div x-show="activeTab === 'campaigns'" x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Campaign Performance') }}</h3>
                            </div>
                            @if(count($campaigns) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Campaign') }}</th>
                                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('CTR') }}</th>
                                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($campaigns as $campaign)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $campaign['campaign_name'] }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['impressions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['ctr'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['conversions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['cost'], 0, ',', ' ') }} Ft</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No campaign data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Ad Groups Tab --}}
                    <div x-show="activeTab === 'adgroups'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Ad Group Performance') }}</h3>
                            </div>
                            @if(count($adGroups) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Campaign') }}</th>
                                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Ad Group') }}</th>
                                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($adGroups as $adGroup)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $adGroup['campaign_name'] }}</td>
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $adGroup['ad_group_name'] }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['cost'], 0, ',', ' ') }} Ft</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No ad group data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Other tabs abbreviated for brevity --}}
                    <div x-show="activeTab === 'hourly'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Performance by Hour') }}</h3>
                            @if(count($hourlyStats) > 0)
                                <div class="grid grid-cols-4 gap-2 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-12">
                                    @foreach($hourlyStats as $stat)
                                        <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ str_pad((string)$stat['hour'], 2, '0', STR_PAD_LEFT) }}:00</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $stat['clicks'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 dark:text-gray-400">{{ __('No hourly data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    <div x-show="activeTab === 'devices'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Performance by Device') }}</h3>
                            @if(count($deviceStats) > 0)
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    @foreach($deviceStats as $device)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $device['device'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($device['clicks']) }} {{ __('clicks') }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 dark:text-gray-400">{{ __('No device data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    <div x-show="activeTab === 'demographics'" x-cloak x-transition>
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('By Gender') }}</h3>
                                @forelse($genderStats as $gender)
                                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $gender['gender'] }}</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($gender['clicks']) }}</span>
                                    </div>
                                @empty
                                    <div class="text-center text-gray-500 dark:text-gray-400">{{ __('No data available.') }}</div>
                                @endforelse
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('By Age') }}</h3>
                                @forelse($ageStats as $age)
                                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $age['age_range'] }}</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($age['clicks']) }}</span>
                                    </div>
                                @empty
                                    <div class="text-center text-gray-500 dark:text-gray-400">{{ __('No data available.') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'geographic'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Performance by Region') }}</h3>
                            @forelse($geographicStats as $geo)
                                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $geo['location_name'] }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($geo['impressions']) }} {{ __('impressions') }}</span>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 dark:text-gray-400">{{ __('No geographic data available.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
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
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('You need to create a team before viewing Google Ads data.') }}</p>
            <a href="{{ route('filament.admin.tenant.registration') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('Create Team') }}
            </a>
        </div>
    @endif
</div>
