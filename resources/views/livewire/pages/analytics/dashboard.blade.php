<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Google Analytics Dashboard') }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('Real-time widgets from Google Analytics.') }}</p>
            </div>
        </div>
    </div>

    @if($team)
        @if($analyticsConfigured)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- Page Views Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\PageViewsWidget::class)
                </div>

                {{-- Visitors Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\VisitorsWidget::class)
                </div>

                {{-- Active Users 1 Day Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\ActiveUsersOneDayWidget::class)
                </div>

                {{-- Active Users 7 Day Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\ActiveUsersSevenDayWidget::class)
                </div>

                {{-- Active Users 28 Day Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\ActiveUsersTwentyEightDayWidget::class)
                </div>

                {{-- Sessions Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\SessionsWidget::class)
                </div>

                {{-- Sessions By Country Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\SessionsByCountryWidget::class)
                </div>

                {{-- Sessions Duration Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\SessionsDurationWidget::class)
                </div>

                {{-- Sessions By Device Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\SessionsByDeviceWidget::class)
                </div>

                {{-- Most Visited Pages Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\MostVisitedPagesWidget::class)
                </div>

                {{-- Top Referrers Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden md:col-span-2">
                    @livewire(\BezhanSalleh\GoogleAnalytics\Widgets\TopReferrersListWidget::class)
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('Google Analytics Not Configured') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('The Google Analytics integration requires a global property ID to be configured in the application settings.') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">{{ __('Please contact your administrator to configure the ANALYTICS_PROPERTY_ID environment variable.') }}</p>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Team Found') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('You need to create a team before viewing the Google Analytics dashboard.') }}</p>
            <a href="{{ route('filament.admin.tenant.registration') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('Create Team') }}
            </a>
        </div>
    @endif
</div>
