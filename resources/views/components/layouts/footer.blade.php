<footer class="bg-white border-t border-gray-200" x-data="{ openSection: null }">
    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-x-8 gap-y-8">
            <!-- Column 1: Logo + Links -->
            <div class="col-span-2 md:col-span-3 lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-5">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10">
                    <span class="text-sm font-semibold text-emerald-600">Kontrolling</span>
                </a>
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('Web traffic analysis and marketing KPIs in one place.') }}
                </p>
                <ul class="space-y-2.5 text-sm text-gray-700">
                    <li><a href="#arak" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Pricing') }}</a></li>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Contact') }}</a></li>
                    <li><a href="#gyik" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('FAQ') }}</a></li>
                </ul>
            </div>

            <!-- Column 2: Features -->
            <div class="col-span-1">
                <button
                    class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                    @click="openSection = openSection === 'funkciok' ? null : 'funkciok'"
                >
                    {{ __('Features') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'funkciok' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">{{ __('Features') }}</h3>
                <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'funkciok' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                    <li><a href="#funkciok" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Dashboard') }}</a></li>
                    <li><a href="#funkciok" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('KPIs') }}</a></li>
                    <li><a href="#funkciok" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Reports') }}</a></li>
                    <li><a href="#funkciok" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Alerts') }}</a></li>
                    <li><a href="#funkciok" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Historical data') }}</a></li>
                </ul>
            </div>

            <!-- Column 3: Integrations -->
            <div class="col-span-1">
                <button
                    class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                    @click="openSection = openSection === 'integraciok' ? null : 'integraciok'"
                >
                    {{ __('Integrations') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'integraciok' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">{{ __('Integrations') }}</h3>
                <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'integraciok' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                    <li><a href="#integraciok" class="text-inherit! hover:text-emerald-600! transition-colors">Google Analytics</a></li>
                    <li><a href="#integraciok" class="text-inherit! hover:text-emerald-600! transition-colors">Search Console</a></li>
                    <li><a href="#integraciok" class="text-inherit! hover:text-emerald-600! transition-colors">Google Ads</a></li>
                </ul>

                <h4 class="text-[15px] font-semibold text-gray-900 mb-4 mt-6">{{ __('Coming Soon') }}</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li>Facebook Ads</li>
                    <li>LinkedIn Ads</li>
                </ul>
            </div>

            <!-- Column 4: Support -->
            <div class="col-span-1">
                <button
                    class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                    @click="openSection = openSection === 'tamogatas' ? null : 'tamogatas'"
                >
                    {{ __('Support') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'tamogatas' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">{{ __('Support') }}</h3>
                <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'tamogatas' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Help') }}</a></li>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Knowledge Base') }}</a></li>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Contact') }}</a></li>
                    <li><a href="#gyik" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('FAQ') }}</a></li>
                </ul>
            </div>

            <!-- Column 5: Cégem360 -->
            <div class="col-span-1">
                <button
                    class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                    @click="openSection = openSection === 'cegem360' ? null : 'cegem360'"
                >
                    Cégem360
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'cegem360' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">Cégem360</h3>
                <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'cegem360' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Home') }}</a></li>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Modules') }}</a></li>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('About Us') }}</a></li>
                    <li><a href="#" class="text-inherit! hover:text-emerald-600! transition-colors">{{ __('Blog') }}</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Sub-footer -->
    <div class="border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-5">
                <!-- Left: Language selector + Social icons -->
                <div class="flex flex-col sm:flex-row items-center gap-5">
                    <!-- Language Selector -->
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:text-gray-900 border border-gray-300 rounded-md transition-colors bg-white"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            {{ app()->getLocale() === 'hu' ? 'Magyar' : 'English' }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div
                            x-show="open"
                            x-transition
                            class="absolute bottom-full left-0 mb-2 w-36 bg-white border border-gray-200 rounded-md shadow-lg overflow-hidden z-10"
                        >
                            <a href="{{ route('language.switch', 'hu') }}" class="block px-3 py-2 text-sm {{ app()->getLocale() === 'hu' ? 'text-gray-900 bg-gray-100' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">Magyar</a>
                            <a href="{{ route('language.switch', 'en') }}" class="block px-3 py-2 text-sm {{ app()->getLocale() === 'en' ? 'text-gray-900 bg-gray-100' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">English</a>
                        </div>
                    </div>

                    <!-- Social Icons -->
                    <div class="flex items-center gap-3">
                        <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Bottom row: Legal links -->
            <div class="mt-5 pt-5 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm text-gray-500">
                    <span class="text-gray-700">{{ __('Cégem360 Kontrolling is a product of Cégem360 Kft.') }}</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm text-gray-500">
                    <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Terms of Service') }}</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Privacy Policy') }}</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Cookie Policy') }}</a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-3 text-center sm:text-left text-sm text-gray-400">
                {{ __('All rights reserved.') }} &copy; {{ date('Y') }} Cégem360
            </div>
        </div>
    </div>
</footer>
