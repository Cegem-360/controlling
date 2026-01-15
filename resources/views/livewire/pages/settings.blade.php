<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Settings') }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('Configure your Google integrations and team settings.') }}</p>
            </div>
        </div>
    </div>

    @if($team)
        {{-- Quick Actions --}}
        <div class="mb-8">
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">{{ __('Quick Actions') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Sync Analytics Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors group flex flex-col">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-linear-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Sync Analytics') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Import latest Google Analytics data') }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button wire:click="performAnalyticsSync" wire:loading.attr="disabled" wire:target="performAnalyticsSync" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition cursor-pointer">
                            <svg wire:loading.remove wire:target="performAnalyticsSync" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg wire:loading wire:target="performAnalyticsSync" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Sync Now') }}
                        </button>
                    </div>
                </div>

                {{-- Sync Search Console Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:border-blue-300 dark:hover:border-blue-700 transition-colors group flex flex-col">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-linear-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Sync Search Console') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Import latest search performance data') }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button wire:click="performSearchConsoleSync" wire:loading.attr="disabled" wire:target="performSearchConsoleSync" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition cursor-pointer">
                            <svg wire:loading.remove wire:target="performSearchConsoleSync" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg wire:loading wire:target="performSearchConsoleSync" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Sync Now') }}
                        </button>
                    </div>
                </div>

                {{-- Google Ads Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:border-amber-300 dark:hover:border-amber-700 transition-colors group flex flex-col">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-linear-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Google Ads') }}</h3>
                            @if($this->getGoogleAdsSettings()?->is_connected)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Sync your advertising data') }}</p>
                            @else
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Connect your Google Ads account') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        @if($this->getGoogleAdsSettings()?->is_connected)
                            <button wire:click="performGoogleAdsSync" wire:loading.attr="disabled" wire:target="performGoogleAdsSync" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition cursor-pointer">
                                <svg wire:loading.remove wire:target="performGoogleAdsSync" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <svg wire:loading wire:target="performGoogleAdsSync" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Sync Now') }}
                            </button>
                        @else
                            <button wire:click="connectGoogleAds" wire:loading.attr="disabled" wire:target="connectGoogleAds" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition cursor-pointer">
                                <svg wire:loading.remove wire:target="connectGoogleAds" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <svg wire:loading wire:target="connectGoogleAds" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Connect') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Configuration Cards --}}
        <div class="space-y-6">
            {{-- Google Analytics Configuration --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-linear-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Google Analytics Configuration') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Configure Google Analytics 4 property settings for this team') }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('GA4 Property ID') }}</label>
                            <input type="text" wire:model="data.property_id" placeholder="123456789" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Your Google Analytics 4 property identifier') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Google Tag ID') }}</label>
                            <input type="text" wire:model="data.google_tag_id" placeholder="G-XXXXXXXXXX" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Your Google Tag Manager ID') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search Console Configuration --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-linear-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Google Search Console Configuration') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Configure Search Console property settings for this team') }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Site URL') }}</label>
                        <input type="url" wire:model="data.site_url" placeholder="https://example.com" class="w-full max-w-xl px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('The verified site URL in your Search Console property') }}</p>
                    </div>
                </div>
            </div>

            {{-- Google Ads Configuration --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-linear-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Google Ads Configuration') }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Configure Google Ads OAuth connection for this team') }}</p>
                            </div>
                        </div>
                        @if($this->getGoogleAdsSettings()?->is_connected)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-sm font-medium text-emerald-700 dark:text-emerald-400">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ __('Connected') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-sm font-medium text-red-700 dark:text-red-400">
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                {{ __('Not Connected') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    @if($this->getGoogleAdsSettings()?->is_connected)
                        @php $googleAdsSettings = $this->getGoogleAdsSettings(); @endphp
                        <div class="space-y-6">
                            {{-- Connection Info --}}
                            <div class="flex items-center gap-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                                <div class="shrink-0">
                                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ __('Your Google Ads account is connected') }}</p>
                                    @if($googleAdsSettings?->last_sync_at)
                                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">{{ __('Last synced') }}: {{ $googleAdsSettings->last_sync_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                                <div>
                                    <button wire:click="disconnectGoogleAds" wire:loading.attr="disabled" wire:target="disconnectGoogleAds" wire:confirm="{{ __('Are you sure you want to disconnect your Google Ads account? This will remove the OAuth connection but keep your synced data.') }}" type="button" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-medium rounded-lg transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        {{ __('Disconnect') }}
                                    </button>
                                </div>
                            </div>

                            {{-- Google Ads Settings Form --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Customer ID') }}</label>
                                    <input type="text" wire:model.blur="googleAdsCustomerId" wire:change="saveGoogleAdsField('customer_id', $event.target.value)" value="{{ $googleAdsSettings?->customer_id }}" placeholder="123-456-7890" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Your Google Ads customer ID') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Manager Customer ID') }}</label>
                                    <input type="text" wire:model.blur="googleAdsManagerId" wire:change="saveGoogleAdsField('manager_customer_id', $event.target.value)" value="{{ $googleAdsSettings?->manager_customer_id }}" placeholder="123-456-7890" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Optional: MCC account ID if using a manager account') }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('Connect Google Ads') }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 max-w-md mx-auto">{{ __('Link your Google Ads account to import campaign performance data and track your advertising ROI.') }}</p>
                            <button wire:click="connectGoogleAds" wire:loading.attr="disabled" wire:target="connectGoogleAds" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition shadow-lg shadow-amber-500/30 cursor-pointer">
                                <svg wire:loading.remove wire:target="connectGoogleAds" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <svg wire:loading wire:target="connectGoogleAds" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Connect Google Ads') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end">
                <button wire:click="save" type="button" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Save Settings') }}
                </button>
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
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('You need to create a team before configuring settings.') }}</p>
            <a href="{{ route('filament.admin.tenant.registration') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('Create Team') }}
            </a>
        </div>
    @endif

    <x-filament-actions::modals />
</div>
