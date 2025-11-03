<x-layouts.app>
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 lg:py-32">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    <span class="block">{{ __('welcome.hero_title_line1') }}</span>
                    <span class="block text-blue-600 dark:text-blue-400 mt-2">{{ __('welcome.hero_title_line2') }}</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-10">
                    {{ __('welcome.hero_description') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                       class="inline-flex items-center justify-center px-8 py-3 text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        {{ __('welcome.hero_cta_admin') }}
                    </a>
                    <a href="#features"
                       class="inline-flex items-center justify-center px-8 py-3 text-base font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border-2 border-blue-600 dark:border-blue-400 transition-colors">
                        {{ __('welcome.hero_cta_features') }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ __('welcome.features_title') }}
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    {{ __('welcome.features_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Customer Management --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('welcome.feature_customer_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('welcome.feature_customer_description') }}
                    </p>
                </div>

                {{-- Sales Pipeline --}}
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-green-600 dark:bg-green-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('welcome.feature_sales_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('welcome.feature_sales_description') }}
                    </p>
                </div>

                {{-- Products & Quotes --}}
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-purple-600 dark:bg-purple-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('welcome.feature_products_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('welcome.feature_products_description') }}
                    </p>
                </div>

                {{-- Invoicing --}}
                <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-orange-600 dark:bg-orange-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('welcome.feature_invoicing_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('welcome.feature_invoicing_description') }}
                    </p>
                </div>

                {{-- Task Management --}}
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-yellow-600 dark:bg-yellow-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('welcome.feature_tasks_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('welcome.feature_tasks_description') }}
                    </p>
                </div>

                {{-- Communication --}}
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-cyan-600 dark:bg-cyan-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ __('welcome.feature_communication_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ __('welcome.feature_communication_description') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Benefits Section --}}
    <section class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ __('welcome.benefits_title') }}
                    </h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="shrink-0">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('welcome.benefit_centralized_title') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ __('welcome.benefit_centralized_description') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="shrink-0">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('welcome.benefit_efficiency_title') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ __('welcome.benefit_efficiency_description') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="shrink-0">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('welcome.benefit_analytics_title') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ __('welcome.benefit_analytics_description') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="shrink-0">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('welcome.benefit_modern_title') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ __('welcome.benefit_modern_description') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 rounded-2xl p-8 text-white shadow-2xl">
                    <div class="text-center">
                        <svg class="w-20 h-20 mx-auto mb-6 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold mb-4">{{ __('welcome.cta_box_title') }}</h3>
                        <p class="text-blue-100 mb-6">
                            {{ __('welcome.cta_box_description') }}
                        </p>
                        <a href="{{ route('filament.admin.pages.dashboard') }}"
                           class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-lg bg-white text-blue-600 hover:bg-gray-100 transition-colors shadow-lg">
                            {{ __('welcome.cta_box_button') }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">100%</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ __('welcome.stats_web_based') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-2">24/7</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ __('welcome.stats_access') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ __('welcome.stats_modern') }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ __('welcome.stats_technology') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-orange-600 dark:text-orange-400 mb-2">{{ __('welcome.stats_secure') }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ __('welcome.stats_data_management') }}</div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
