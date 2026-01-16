<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Google Ads Dashboard') }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $this->getDateRangeLabel() }}</p>
            </div>
        </div>
    </div>

    @if($team)
        <div class="flex flex-col gap-6">
            {{-- Date Range Filter --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Period') }}:</span>
                <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden shadow-sm flex-wrap">
                    <button
                        wire:click="setDateRange('7_days')"
                        class="{{ $dateRangeType === '7_days' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                    >
                        {{ __('7 days') }}
                    </button>
                    <button
                        wire:click="setDateRange('28_days')"
                        class="{{ $dateRangeType === '28_days' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                    >
                        {{ __('28 days') }}
                    </button>
                    <button
                        wire:click="setDateRange('last_month')"
                        class="{{ $dateRangeType === 'last_month' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                    >
                        {{ __('Last month') }}
                    </button>
                    <button
                        wire:click="setDateRange('this_month')"
                        class="{{ $dateRangeType === 'this_month' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                    >
                        {{ __('This month') }}
                    </button>
                    <button
                        wire:click="setDateRange('3_months')"
                        class="{{ $dateRangeType === '3_months' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors"
                    >
                        {{ __('3 months') }}
                    </button>
                </div>
            </div>

            {{-- Main KPI Stats with comparison --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Key Performance Indicators') }}</h2>
                <div class="grid grid-cols-2 gap-6 lg:grid-cols-3 xl:grid-cols-6">
                    {{-- Impressions --}}
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_impressions'] ?? 0) }}</div>
                        @php $change = $this->getPercentageChange('total_impressions'); @endphp
                        <div class="flex items-center gap-1 text-sm {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%</span>
                            <span class="text-gray-400">{{ __('vs previous') }}</span>
                        </div>
                    </div>

                    {{-- Clicks --}}
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_clicks'] ?? 0) }}</div>
                        @php $change = $this->getPercentageChange('total_clicks'); @endphp
                        <div class="flex items-center gap-1 text-sm {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%</span>
                            <span class="text-gray-400">{{ __('vs previous') }}</span>
                        </div>
                    </div>

                    {{-- Avg CPC --}}
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Avg. CPC') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['avg_cpc'] ?? 0, 2, ',', ' ') }} Ft</div>
                        @php $change = $this->getPercentageChange('avg_cpc'); @endphp
                        <div class="flex items-center gap-1 text-sm {{ $change <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%</span>
                            <span class="text-gray-400">{{ __('vs previous') }}</span>
                        </div>
                    </div>

                    {{-- Cost --}}
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_cost'] ?? 0, 0, ',', ' ') }} Ft</div>
                        @php $change = $this->getPercentageChange('total_cost'); @endphp
                        <div class="flex items-center gap-1 text-sm {{ $change <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%</span>
                            <span class="text-gray-400">{{ __('vs previous') }}</span>
                        </div>
                    </div>

                    {{-- Conversions --}}
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_conversions'] ?? 0, 0) }}</div>
                        @php $change = $this->getPercentageChange('total_conversions'); @endphp
                        <div class="flex items-center gap-1 text-sm {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%</span>
                            <span class="text-gray-400">{{ __('vs previous') }}</span>
                        </div>
                    </div>

                    {{-- Cost per Conversion --}}
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost/Conv.') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['cost_per_conversion'] ?? 0, 2, ',', ' ') }} Ft</div>
                        @php $change = $this->getPercentageChange('cost_per_conversion'); @endphp
                        <div class="flex items-center gap-1 text-sm {{ $change <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%</span>
                            <span class="text-gray-400">{{ __('vs previous') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div x-data="{ activeTab: 'campaigns' }" class="flex flex-col gap-6">
                {{-- Tab Headers --}}
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-4 overflow-x-auto" aria-label="Tabs">
                        <button @click="activeTab = 'campaigns'" :class="activeTab === 'campaigns' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Campaigns') }}</button>
                        <button @click="activeTab = 'adgroups'" :class="activeTab === 'adgroups' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Ad Groups') }}</button>
                        <button @click="activeTab = 'daily'" :class="activeTab === 'daily' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Daily') }}</button>
                        <button @click="activeTab = 'hourly'" :class="activeTab === 'hourly' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Hourly') }}</button>
                        <button @click="activeTab = 'devices'" :class="activeTab === 'devices' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Devices') }}</button>
                        <button @click="activeTab = 'demographics'" :class="activeTab === 'demographics' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Demographics') }}</button>
                        <button @click="activeTab = 'geographic'" :class="activeTab === 'geographic' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Geographic') }}</button>
                        <button @click="activeTab = 'monthly'" :class="activeTab === 'monthly' ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors">{{ __('Monthly Trends') }}</button>
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
                                        <thead class="bg-blue-600 text-white">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Campaign') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Avg. CPC') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('CTR') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conversions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost/Conv.') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($campaigns as $campaign)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $campaign['campaign_name'] }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($campaign['impressions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($campaign['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['avg_cpc'], 2, ',', ' ') }} Ft</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['ctr'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right font-medium {{ $campaign['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($campaign['conversions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $campaign['conversions'] > 0 ? number_format($campaign['cost_per_conversion'], 2, ',', ' ') . ' Ft' : '-' }}</td>
                                                    <td class="px-4 py-3 text-right {{ $campaign['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($campaign['conversion_rate'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['cost'], 0, ',', ' ') }} Ft</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-100 dark:bg-gray-700 font-semibold">
                                            <tr>
                                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ __('Total') }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_impressions'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_clicks'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['avg_cpc'] ?? 0, 2, ',', ' ') }} Ft</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['avg_ctr'] ?? 0, 2) }}%</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_conversions'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['cost_per_conversion'] ?? 0, 2, ',', ' ') }} Ft</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['conversion_rate'] ?? 0, 2) }}%</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_cost'] ?? 0, 0, ',', ' ') }} Ft</td>
                                            </tr>
                                        </tfoot>
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
                                        <thead class="bg-blue-600 text-white">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Campaign') }}</th>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Ad Group') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Avg. CPC') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('CTR') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conversions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost/Conv.') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($adGroups as $adGroup)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ Str::limit($adGroup['campaign_name'], 25) }}</td>
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $adGroup['ad_group_name'] }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($adGroup['impressions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($adGroup['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['avg_cpc'], 2, ',', ' ') }} Ft</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['ctr'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right font-medium {{ $adGroup['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($adGroup['conversions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $adGroup['conversions'] > 0 ? number_format($adGroup['cost_per_conversion'], 2, ',', ' ') . ' Ft' : '-' }}</td>
                                                    <td class="px-4 py-3 text-right {{ $adGroup['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($adGroup['conversion_rate'], 2) }}%</td>
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

                    {{-- Daily Tab --}}
                    <div x-show="activeTab === 'daily'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Daily Performance') }}</h3>
                            </div>
                            @if(count($dailyStats) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-blue-600 text-white">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Date') }}</th>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Day') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Avg. CPC') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('CTR') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conversions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost/Conv.') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($dailyStats as $day)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $day['date_formatted'] }}</td>
                                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $day['day_name'] }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($day['impressions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($day['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($day['avg_cpc'], 2, ',', ' ') }} Ft</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($day['ctr'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right font-medium {{ $day['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($day['conversions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $day['conversions'] > 0 ? number_format($day['cost_per_conversion'], 2, ',', ' ') . ' Ft' : '-' }}</td>
                                                    <td class="px-4 py-3 text-right {{ $day['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($day['conversion_rate'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($day['cost'], 0, ',', ' ') }} Ft</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-100 dark:bg-gray-700 font-semibold">
                                            <tr>
                                                <td class="px-4 py-3 text-gray-900 dark:text-white" colspan="2">{{ __('Total') }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_impressions'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_clicks'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['avg_cpc'] ?? 0, 2, ',', ' ') }} Ft</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['avg_ctr'] ?? 0, 2) }}%</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_conversions'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['cost_per_conversion'] ?? 0, 2, ',', ' ') }} Ft</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['conversion_rate'] ?? 0, 2) }}%</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_cost'] ?? 0, 0, ',', ' ') }} Ft</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No daily data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Hourly Tab --}}
                    <div x-show="activeTab === 'hourly'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Performance by Hour') }}</h3>
                            </div>
                            @if(count($hourlyStats) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-blue-600 text-white">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Hour') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Avg. CPC') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('CTR') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conversions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost/Conv.') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($hourlyStats as $hour)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ str_pad((string)$hour['hour'], 2, '0', STR_PAD_LEFT) }}:00</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($hour['impressions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($hour['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($hour['avg_cpc'], 2, ',', ' ') }} Ft</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($hour['ctr'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right font-medium {{ $hour['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($hour['conversions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $hour['conversions'] > 0 ? number_format($hour['cost_per_conversion'], 2, ',', ' ') . ' Ft' : '-' }}</td>
                                                    <td class="px-4 py-3 text-right {{ $hour['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($hour['conversion_rate'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($hour['cost'], 0, ',', ' ') }} Ft</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-100 dark:bg-gray-700 font-semibold">
                                            <tr>
                                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ __('Total') }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_impressions'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_clicks'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['avg_cpc'] ?? 0, 2, ',', ' ') }} Ft</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['avg_ctr'] ?? 0, 2) }}%</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_conversions'] ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['cost_per_conversion'] ?? 0, 2, ',', ' ') }} Ft</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['conversion_rate'] ?? 0, 2) }}%</td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($stats['total_cost'] ?? 0, 0, ',', ' ') }} Ft</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No hourly data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Devices Tab --}}
                    <div x-show="activeTab === 'devices'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Performance by Device') }}</h3>
                            </div>
                            @if(count($deviceStats) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-blue-600 text-white">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Device') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Avg. CPC') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('CTR') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conversions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost/Conv.') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($deviceStats as $device)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $device['device'] }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($device['impressions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($device['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['avg_cpc'], 2, ',', ' ') }} Ft</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['ctr'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right font-medium {{ $device['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($device['conversions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $device['conversions'] > 0 ? number_format($device['cost_per_conversion'], 2, ',', ' ') . ' Ft' : '-' }}</td>
                                                    <td class="px-4 py-3 text-right {{ $device['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($device['conversion_rate'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['cost'], 0, ',', ' ') }} Ft</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No device data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Demographics Tab --}}
                    <div x-show="activeTab === 'demographics'" x-cloak x-transition>
                        <div class="grid gap-6 lg:grid-cols-2">
                            {{-- Gender Stats --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('By Gender') }}</h3>
                                </div>
                                @if(count($genderStats) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-blue-600 text-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-medium">{{ __('Gender') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv.') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($genderStats as $gender)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $gender['gender'] }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($gender['impressions']) }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($gender['clicks']) }}</td>
                                                        <td class="px-4 py-3 text-right font-medium {{ $gender['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($gender['conversions']) }}</td>
                                                        <td class="px-4 py-3 text-right {{ $gender['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($gender['conversion_rate'], 2) }}%</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($gender['cost'], 0, ',', ' ') }} Ft</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No gender data available.') }}</div>
                                @endif
                            </div>

                            {{-- Age Stats --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('By Age') }}</h3>
                                </div>
                                @if(count($ageStats) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-blue-600 text-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-medium">{{ __('Age') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv.') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($ageStats as $age)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $age['age_range'] }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($age['impressions']) }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($age['clicks']) }}</td>
                                                        <td class="px-4 py-3 text-right font-medium {{ $age['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($age['conversions']) }}</td>
                                                        <td class="px-4 py-3 text-right {{ $age['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($age['conversion_rate'], 2) }}%</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($age['cost'], 0, ',', ' ') }} Ft</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No age data available.') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Geographic Tab --}}
                    <div x-show="activeTab === 'geographic'" x-cloak x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Performance by Region') }}</h3>
                            </div>
                            @if(count($geographicStats) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-blue-600 text-white">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">{{ __('Region') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Avg. CPC') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('CTR') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conversions') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost/Conv.') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($geographicStats as $geo)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $geo['location_name'] }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($geo['impressions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($geo['clicks']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['avg_cpc'], 2, ',', ' ') }} Ft</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['ctr'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right font-medium {{ $geo['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($geo['conversions']) }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $geo['conversions'] > 0 ? number_format($geo['cost_per_conversion'], 2, ',', ' ') . ' Ft' : '-' }}</td>
                                                    <td class="px-4 py-3 text-right {{ $geo['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($geo['conversion_rate'], 2) }}%</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['cost'], 0, ',', ' ') }} Ft</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No geographic data available.') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Monthly Trends Tab --}}
                    <div x-show="activeTab === 'monthly'" x-cloak x-transition>
                        <div class="grid gap-6 lg:grid-cols-2">
                            {{-- Current Year --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Current Year') }} ({{ now()->year }})</h3>
                                </div>
                                @if(count($monthlyStats) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-blue-600 text-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-medium">{{ __('Month') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv.') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($monthlyStats as $month)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $month['month'] }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">{{ number_format($month['impressions']) }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($month['clicks']) }}</td>
                                                        <td class="px-4 py-3 text-right font-medium {{ $month['conversions'] > 0 ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($month['conversions']) }}</td>
                                                        <td class="px-4 py-3 text-right {{ $month['conversion_rate'] >= 5 ? 'text-green-600 bg-green-50 dark:bg-green-900/20 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($month['conversion_rate'], 2) }}%</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($month['cost'], 0, ',', ' ') }} Ft</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No data available for current year.') }}</div>
                                @endif
                            </div>

                            {{-- Previous Year --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Previous Year') }} ({{ now()->subYear()->year }})</h3>
                                </div>
                                @if(count($previousYearMonthlyStats) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-600 text-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-medium">{{ __('Month') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Impressions') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Clicks') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv.') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Conv. Rate') }}</th>
                                                    <th class="px-4 py-3 text-right font-medium">{{ __('Cost') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($previousYearMonthlyStats as $month)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $month['month'] }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($month['impressions']) }}</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($month['clicks']) }}</td>
                                                        <td class="px-4 py-3 text-right font-medium {{ $month['conversions'] > 0 ? 'text-green-600' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($month['conversions']) }}</td>
                                                        <td class="px-4 py-3 text-right {{ $month['conversion_rate'] >= 5 ? 'text-green-600 font-medium' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format($month['conversion_rate'], 2) }}%</td>
                                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($month['cost'], 0, ',', ' ') }} Ft</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No data available for previous year.') }}</div>
                                @endif
                            </div>
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
            <a href="{{ route('filament.admin.tenant.registration') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('Create Team') }}
            </a>
        </div>
    @endif
</div>
