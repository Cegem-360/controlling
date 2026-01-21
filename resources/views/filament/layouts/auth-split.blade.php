<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen font-sans antialiased">
    <div class="flex min-h-screen">
        <!-- Left side - Form -->
        <div class="flex w-full flex-col bg-white lg:w-1/2">
            <!-- Logo header -->
            <div class="px-6 py-6 lg:px-12">
                <a href="{{ route('home') }}">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10">
                </a>
            </div>
            <!-- Main content area - centered -->
            <div class="flex flex-1 flex-col items-center justify-center px-6 pb-6 lg:px-12">
                <div class="w-full max-w-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Right side - Illustration with floating elements -->
        <div class="hidden bg-emerald-600 lg:flex lg:w-1/2 lg:items-center lg:justify-center relative overflow-hidden">
            <!-- Concentric circles behind the UI panel -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-[800px] h-[800px] border-2 border-white/20 rounded-full"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-[600px] h-[600px] border-2 border-white/25 rounded-full"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-[400px] h-[400px] border-2 border-white/20 rounded-full"></div>
            </div>

            <div class="relative w-full max-w-2xl px-12">
                <!-- Dashboard mockup card -->
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 relative z-10">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6">
                        <!-- Stats grid -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-500 mb-1">{{ __('Monthly revenue') }}</div>
                                <div class="text-xl font-bold text-gray-900">48.2M Ft</div>
                                <div class="text-xs text-green-600">+15%</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-500 mb-1">{{ __('Expenses') }}</div>
                                <div class="text-xl font-bold text-gray-900">32.1M Ft</div>
                                <div class="text-xs text-red-600">+8%</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-500 mb-1">{{ __('Profit') }}</div>
                                <div class="text-xl font-bold text-gray-900">16.1M Ft</div>
                                <div class="text-xs text-green-600">+24%</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="text-xs text-gray-500 mb-1">{{ __('Margin') }}</div>
                                <div class="text-xl font-bold text-gray-900">33.4%</div>
                                <div class="text-xs text-green-600">+2.1%</div>
                            </div>
                        </div>
                        <!-- Chart mockup -->
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-gray-900">{{ __('Revenue vs Budget') }}</span>
                                <span class="text-xs text-gray-500">Q4 2024</span>
                            </div>
                            <div class="flex items-end gap-2 h-16">
                                <div class="flex-1 flex flex-col gap-1">
                                    <div class="bg-emerald-500 rounded h-10"></div>
                                    <div class="bg-emerald-200 rounded h-8"></div>
                                </div>
                                <div class="flex-1 flex flex-col gap-1">
                                    <div class="bg-emerald-500 rounded h-12"></div>
                                    <div class="bg-emerald-200 rounded h-10"></div>
                                </div>
                                <div class="flex-1 flex flex-col gap-1">
                                    <div class="bg-emerald-500 rounded h-14"></div>
                                    <div class="bg-emerald-200 rounded h-11"></div>
                                </div>
                                <div class="flex-1 flex flex-col gap-1">
                                    <div class="bg-emerald-500 rounded h-16"></div>
                                    <div class="bg-emerald-200 rounded h-12"></div>
                                </div>
                            </div>
                            <div class="flex justify-between mt-2 text-xs text-gray-400">
                                <span>Oct</span>
                                <span>Nov</span>
                                <span>Dec</span>
                                <span>Jan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating notification - Budget alert -->
                <div class="absolute -left-8 top-1/4 bg-white rounded-lg shadow-lg p-3 border border-gray-100 animate-pulse z-20">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('Target reached!') }}</div>
                            <div class="text-xs text-gray-500">{{ __('Q4 revenue goal') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Floating notification - KPI -->
                <div class="absolute -right-4 top-1/2 bg-white rounded-lg shadow-lg p-3 border border-gray-100 z-20">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('KPI Update') }}</div>
                            <div class="text-xs text-gray-500">{{ __('ROI increased by 12%') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Floating notification - Report -->
                <div class="absolute left-1/3 -bottom-4 bg-white rounded-lg shadow-lg p-3 border border-gray-100 z-20">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('New report') }}</div>
                            <div class="text-xs text-gray-500">{{ __('January forecast ready') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative circles - positioned relative to the outer container -->
            <div class="absolute top-16 right-16 w-24 h-24 border-2 border-white/30 rounded-full"></div>
            <div class="absolute bottom-24 left-12 w-16 h-16 bg-emerald-400/40 rounded-full"></div>
            <div class="absolute top-1/3 right-1/3 w-8 h-8 bg-white/30 rounded-full"></div>
            <div class="absolute bottom-1/3 right-12 w-10 h-10 border-2 border-white/25 rounded-full"></div>
            <div class="absolute top-24 left-16 w-6 h-6 bg-white/35 rounded-full"></div>

            <!-- Decorative wave at bottom -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2">
                <svg class="h-8 w-72 text-emerald-300 opacity-70" viewBox="0 0 200 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M0 10 Q 20 0, 40 10 T 80 10 T 120 10 T 160 10 T 200 10"/>
                </svg>
            </div>
        </div>
    </div>

    @filamentScripts
    @vite('resources/js/app.js')
</body>

</html>
