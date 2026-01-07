<x-filament-panels::page>
    <div class="flex flex-col gap-6">
        {{-- Date Range Filter --}}
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Period') }}:</span>
            <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden shadow-sm">
                <button
                    wire:click="setDateRange('7_days')"
                    class="{{ $dateRangeType === '7_days' ? 'bg-amber-600 text-white hover:bg-amber-700 dark:bg-amber-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                >
                    {{ __('7 days') }}
                </button>
                <button
                    wire:click="setDateRange('28_days')"
                    class="{{ $dateRangeType === '28_days' ? 'bg-amber-600 text-white hover:bg-amber-700 dark:bg-amber-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors border-r border-gray-300 dark:border-gray-600"
                >
                    {{ __('28 days') }}
                </button>
                <button
                    wire:click="setDateRange('3_months')"
                    class="{{ $dateRangeType === '3_months' ? 'bg-amber-600 text-white hover:bg-amber-700 dark:bg-amber-500 dark:text-gray-900' : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }} px-4 py-2 text-sm font-medium transition-colors"
                >
                    {{ __('3 months') }}
                </button>
            </div>
        </div>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_impressions'] ?? 0) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_clicks'] ?? 0) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_cost'] ?? 0, 0, ',', ' ') }} Ft</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_conversions'] ?? 0, 0) }}</div>
            </x-filament::section>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Avg. CTR') }}</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_ctr'] ?? 0, 2) }}%</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Avg. CPC') }}</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_cpc'] ?? 0, 2, ',', ' ') }} Ft</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost / Conversion') }}</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['cost_per_conversion'] ?? 0, 2, ',', ' ') }} Ft</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Conversion Rate') }}</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_conversion_rate'] ?? 0, 2) }}%</div>
            </x-filament::section>
        </div>

        {{-- Tabs Navigation --}}
        <div x-data="{ activeTab: 'campaigns' }" class="flex flex-col gap-6">
            {{-- Tab Headers --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex gap-4 overflow-x-auto" aria-label="Tabs">
                    <button
                        @click="activeTab = 'campaigns'"
                        :class="activeTab === 'campaigns' ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                    >
                        {{ __('Campaigns') }}
                    </button>
                    <button
                        @click="activeTab = 'adgroups'"
                        :class="activeTab === 'adgroups' ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                    >
                        {{ __('Ad Groups') }}
                    </button>
                    <button
                        @click="activeTab = 'hourly'"
                        :class="activeTab === 'hourly' ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                    >
                        {{ __('By Hour') }}
                    </button>
                    <button
                        @click="activeTab = 'devices'"
                        :class="activeTab === 'devices' ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                    >
                        {{ __('Devices') }}
                    </button>
                    <button
                        @click="activeTab = 'demographics'"
                        :class="activeTab === 'demographics' ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                    >
                        {{ __('Demographics') }}
                    </button>
                    <button
                        @click="activeTab = 'geographic'"
                        :class="activeTab === 'geographic' ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                    >
                        {{ __('Geographic') }}
                    </button>
                </nav>
            </div>

            {{-- Tab Contents --}}
            <div>
                {{-- Campaigns Tab --}}
                <div x-show="activeTab === 'campaigns'" x-transition>
                    <x-filament::section>
                        <x-slot name="heading">{{ __('Campaign Performance') }}</x-slot>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Campaign') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('CTR') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conv. Rate') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost/Conv.') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($campaigns as $campaign)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $campaign['campaign_name'] }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['impressions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['clicks']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['ctr'], 2) }}%</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['conversions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['conversion_rate'], 2) }}%</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['cost'], 0, ',', ' ') }} Ft</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campaign['cost_per_conversion'], 0, ',', ' ') }} Ft</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No campaign data available.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-filament::section>
                </div>

                {{-- Ad Groups Tab --}}
                <div x-show="activeTab === 'adgroups'" x-cloak x-transition>
                    <x-filament::section>
                        <x-slot name="heading">{{ __('Ad Group Performance') }}</x-slot>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Campaign') }}</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Ad Group') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('CTR') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($adGroups as $adGroup)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $adGroup['campaign_name'] }}</td>
                                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $adGroup['ad_group_name'] }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['impressions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['clicks']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['ctr'], 2) }}%</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['conversions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($adGroup['cost'], 0, ',', ' ') }} Ft</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No ad group data available.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-filament::section>
                </div>

                {{-- Hourly Tab --}}
                <div x-show="activeTab === 'hourly'" x-cloak x-transition>
                    <x-filament::section>
                        <x-slot name="heading">{{ __('Performance by Hour of Day') }}</x-slot>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Hour') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($hourlyStats as $stat)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ str_pad((string)$stat['hour'], 2, '0', STR_PAD_LEFT) }}:00</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($stat['impressions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($stat['clicks']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($stat['cost'], 0, ',', ' ') }} Ft</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($stat['conversions']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No hourly data available.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-filament::section>
                </div>

                {{-- Devices Tab --}}
                <div x-show="activeTab === 'devices'" x-cloak x-transition>
                    <x-filament::section>
                        <x-slot name="heading">{{ __('Performance by Device') }}</x-slot>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Device') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('CTR') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conv. Rate') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($deviceStats as $device)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $device['device'] }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['impressions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['clicks']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['ctr'], 2) }}%</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['conversions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['conversion_rate'], 2) }}%</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($device['cost'], 0, ',', ' ') }} Ft</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No device data available.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-filament::section>
                </div>

                {{-- Demographics Tab --}}
                <div x-show="activeTab === 'demographics'" x-cloak x-transition>
                    <div class="grid gap-6 lg:grid-cols-2">
                        {{-- Gender Stats --}}
                        <x-filament::section>
                            <x-slot name="heading">{{ __('Performance by Gender') }}</x-slot>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Gender') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($genderStats as $gender)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $gender['gender'] }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($gender['impressions']) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($gender['clicks']) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($gender['conversions']) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($gender['cost'], 0, ',', ' ') }} Ft</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No gender data available.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </x-filament::section>

                        {{-- Age Stats --}}
                        <x-filament::section>
                            <x-slot name="heading">{{ __('Performance by Age') }}</x-slot>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Age Range') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($ageStats as $age)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $age['age_range'] }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($age['impressions']) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($age['clicks']) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($age['conversions']) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($age['cost'], 0, ',', ' ') }} Ft</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No age data available.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </x-filament::section>
                    </div>
                </div>

                {{-- Geographic Tab --}}
                <div x-show="activeTab === 'geographic'" x-cloak x-transition>
                    <x-filament::section>
                        <x-slot name="heading">{{ __('Performance by Region') }}</x-slot>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Location') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Impressions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Clicks') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('CTR') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conversions') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Conv. Rate') }}</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($geographicStats as $geo)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $geo['location_name'] }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['impressions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['clicks']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['ctr'], 2) }}%</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['conversions']) }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['conversion_rate'], 2) }}%</td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($geo['cost'], 0, ',', ' ') }} Ft</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No geographic data available.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-filament::section>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
