@php
    $linkClasses = 'text-inherit! hover:text-emerald-600! transition-colors';
    $headingClasses = 'text-[15px] font-semibold text-gray-900 mb-4';

    $footerSections = [
        'funkciok' => [
            'title' => __('Features'),
            'links' => [
                ['href' => '#funkciok', 'label' => __('Dashboard')],
                ['href' => '#funkciok', 'label' => __('KPIs')],
                ['href' => '#funkciok', 'label' => __('Reports')],
                ['href' => '#funkciok', 'label' => __('Alerts')],
                ['href' => '#funkciok', 'label' => __('Historical data')],
            ],
        ],
        'integraciok' => [
            'title' => __('Integrations'),
            'links' => [
                ['href' => '#integraciok', 'label' => 'Google Analytics'],
                ['href' => '#integraciok', 'label' => 'Search Console'],
                ['href' => '#integraciok', 'label' => 'Google Ads'],
            ],
        ],
        'tamogatas' => [
            'title' => __('Support'),
            'links' => [
                ['href' => '#', 'label' => __('Help')],
                ['href' => '#', 'label' => __('Knowledge Base')],
                ['href' => '#', 'label' => __('Contact')],
                ['href' => '#gyik', 'label' => __('FAQ')],
            ],
        ],
        'cegem360' => [
            'title' => 'Cégem360',
            'links' => [
                ['href' => '#', 'label' => __('Home')],
                ['href' => '#', 'label' => __('Modules')],
                ['href' => '#', 'label' => __('About Us')],
                ['href' => '#', 'label' => __('Blog')],
            ],
        ],
    ];
@endphp

<footer class="bg-white border-t border-gray-200" x-data="{ openSection: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
            <div class="col-span-2 md:col-span-3 lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-5">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="{{ config('app.name') }}"
                        class="h-10">
                    <span class="text-sm font-semibold text-emerald-600">Kontrolling</span>
                </a>
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('Web traffic analysis and marketing KPIs in one place.') }}
                </p>
                <ul class="space-y-2.5 text-sm text-gray-700">
                    <li><a href="#arak" class="{{ $linkClasses }}">{{ __('Pricing') }}</a></li>
                    <li><a href="#" class="{{ $linkClasses }}">{{ __('Contact') }}</a></li>
                    <li><a href="#gyik" class="{{ $linkClasses }}">{{ __('FAQ') }}</a></li>
                </ul>
            </div>

            @foreach ($footerSections as $sectionKey => $section)
                <div class="col-span-1">
                    <button
                        class="lg:hidden w-full flex items-center justify-between {{ $headingClasses }}"
                        @click="openSection = openSection === '{{ $sectionKey }}' ? null : '{{ $sectionKey }}'"
                    >
                        {{ $section['title'] }}
                        <x-heroicon-m-chevron-down
                            class="w-4 h-4 transition-transform"
                            ::class="{ 'rotate-180': openSection === '{{ $sectionKey }}' }"
                        />
                    </button>
                    <h3 class="hidden lg:block {{ $headingClasses }}">{{ $section['title'] }}</h3>
                    <ul
                        class="space-y-2.5 text-sm text-gray-700"
                        x-show="openSection === '{{ $sectionKey }}' || window.innerWidth >= 1024"
                        x-collapse.duration.300ms
                    >
                        @foreach ($section['links'] as $link)
                            <li><a href="{{ $link['href'] }}" class="{{ $linkClasses }}">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>

                    @if ($sectionKey === 'integraciok')
                        <h4 class="{{ $headingClasses }} mt-6">{{ __('Coming Soon') }}</h4>
                        <ul class="space-y-2.5 text-sm text-gray-500">
                            <li>Facebook Ads</li>
                            <li>LinkedIn Ads</li>
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <x-language-switcher />

                <div class="flex items-center gap-3">
                    <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="mt-5 pt-5 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-sm text-gray-700">{{ __('Cégem360 Kontrolling is a product of Cégem360 Kft.') }}</span>
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm text-gray-500">
                    <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Terms of Service') }}</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Privacy Policy') }}</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Cookie Policy') }}</a>
                </div>
            </div>

            <div class="mt-3 text-center sm:text-left text-sm text-gray-400">
                {{ __('All rights reserved.') }} &copy; {{ date('Y') }} Cégem360 Kft.
            </div>
        </div>
    </div>
</footer>
