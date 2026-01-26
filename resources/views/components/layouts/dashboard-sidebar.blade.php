@php
    $hasTeam = auth()->user()->teams->first() !== null;
    $linkClasses = 'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition';
    $activeLinkClasses = 'bg-white/10 text-white';
    $inactiveLinkClasses = 'text-gray-300 hover:bg-white/5 hover:text-white';
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 w-60 bg-[#292F4C] text-white flex flex-col transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0"
    :class="{
        'lg:-translate-x-full': !sidebarOpen,
        'lg:translate-x-0': sidebarOpen,
        '-translate-x-full': !mobileMenuOpen,
        'translate-x-0': mobileMenuOpen
    }">
    {{-- Logo area --}}
    <div class="h-16 flex items-center px-4 border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="{{ config('app.name') }}"
                class="h-8 brightness-0 invert">
            <span class="text-sm font-semibold text-emerald-400">Kontrolling</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
        {{-- Main navigation --}}
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Navigation') }}
            </h3>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="{{ $linkClasses }} {{ request()->routeIs('dashboard') ? $activeLinkClasses : $inactiveLinkClasses }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Home') }}
                    </a>
                </li>
            </ul>
        </div>

        @if ($hasTeam)
            {{-- Analytics section --}}
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Analytics') }}
                </h3>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('analytics.general') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('analytics.general') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            {{ __('General Analytics') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('analytics.statistics') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('analytics.statistics') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            {{ __('Analytics Statistics') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('analytics.dashboard') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('analytics.dashboard') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            {{ __('Google Analytics Dashboard') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Search Console section --}}
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    {{ __('Search Console') }}</h3>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('search-console.general') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('search-console.general') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                                <path stroke-linecap="round" stroke-width="2" d="M16 16l5 5" />
                            </svg>
                            {{ __('General Search Console') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('search-console.pages') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('search-console.pages') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('Search Pages') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('search-console.queries') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('search-console.queries') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Search Queries') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Google Ads section --}}
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    {{ __('Google Ads') }}</h3>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('google-ads.dashboard') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('google-ads.dashboard') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Google Ads Dashboard') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- KPIs section --}}
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('KPIs') }}
                </h3>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('kpis.index') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('kpis.index') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            {{ __('KPI Goals') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Configuration section --}}
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    {{ __('Configuration') }}</h3>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('settings') }}"
                            class="{{ $linkClasses }} {{ request()->routeIs('settings') ? $activeLinkClasses : $inactiveLinkClasses }}">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('Settings') }}
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        {{-- Quick links --}}
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Quick Links') }}
            </h3>
            <ul class="space-y-1">
                @php
                    $externalLinkClasses = $linkClasses . ' ' . $inactiveLinkClasses . ' group';
                    $externalLinkIcon =
                        '<svg class="w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>';
                @endphp
                <li>
                    <a href="https://cegem360.eu/modules" target="_blank" class="{{ $externalLinkClasses }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Cégem360 Modulok
                        {!! $externalLinkIcon !!}
                    </a>
                </li>
                <li>
                    <a href="https://cegem360.eu/admin/profile" target="_blank" class="{{ $externalLinkClasses }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Account Settings') }}
                        {!! $externalLinkIcon !!}
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    {{-- User section at bottom --}}
    @auth
        @php $user = auth()->user(); @endphp
        <div class="border-t border-white/10 p-4">
            <a href="https://cegem360.eu/admin/profile" target="_blank"
                class="{{ $linkClasses }} {{ $inactiveLinkClasses }}">
                <div
                    class="w-8 h-8 rounded-full bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-semibold text-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="truncate font-medium">{{ $user->name }}</p>
                    <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                </div>
            </a>
        </div>
    @endauth
</aside>
