<div>
    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white font-heading">Üdvözöljük,
            {{ auth()->user()->name }}!</h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Itt láthatja a csapataihoz tartozó analitikai összefoglalót.</p>
    </div>

    {{-- Teams grid --}}
    @if ($teams->count() > 0)
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($teams as $team)
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                    {{-- Card header --}}
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($team->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h3>
                                    @if ($team->settings?->site_url)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $team->settings->site_url }}</p>
                                    @endif
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                Aktív
                            </span>
                        </div>
                    </div>

                    {{-- Integrations status --}}
                    <div class="p-6 space-y-4">
                        {{-- Google Analytics --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24"
                                        fill="none">
                                        <path d="M22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22"
                                            stroke="currentColor" stroke-width="2" />
                                        <path d="M12 12L12 6" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                        <circle cx="12" cy="12" r="2" fill="currentColor" />
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Google Analytics</span>
                            </div>
                            @if ($team->settings?->property_id)
                                <span
                                    class="inline-flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Csatlakoztatva
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Nincs csatlakoztatva
                                </span>
                            @endif
                        </div>

                        {{-- Search Console --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24"
                                        fill="none">
                                        <circle cx="11" cy="11" r="7" stroke="currentColor"
                                            stroke-width="2" />
                                        <path d="M16 16L21 21" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Search Console</span>
                            </div>
                            @if ($team->settings?->site_url)
                                <span
                                    class="inline-flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Csatlakoztatva
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Nincs csatlakoztatva
                                </span>
                            @endif
                        </div>

                        {{-- Google Ads --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" viewBox="0 0 24 24"
                                        fill="none">
                                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Google Ads</span>
                            </div>
                            @if ($team->googleAdsSettings?->is_connected)
                                <span
                                    class="inline-flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Csatlakoztatva
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Nincs csatlakoztatva
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Card footer --}}
                    <div
                        class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('filament.admin.pages.dashboard', ['tenant' => $team->slug]) }}"
                            class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">
                            Dashboard megnyitása
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach

        </div>
    @else
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-16 px-4">
            <div
                class="w-24 h-24 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Még nincs csapata</h2>
            <p class="text-gray-600 dark:text-gray-400 text-center max-w-md mb-8">
                Hozza létre első csapatát, és kezdje el a weboldalai teljesítményének követését a Google Analytics,
                Search Console és Google Ads adatokkal.
            </p>
            <a href="{{ route('filament.admin.tenant.registration') }}"
                class="inline-flex items-center gap-2 px-6 py-3 text-base font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-full transition shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Első csapat létrehozása
            </a>
        </div>
    @endif
</div>
