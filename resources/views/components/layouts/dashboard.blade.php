<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title ?? config('app.name') }}</title>

        {{-- Monday.com / Vibe Design System Fonts --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles
        @vite('resources/css/app.css')

    </head>

    <body class="antialiased bg-gray-50 dark:bg-gray-900" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
        <div class="min-h-screen flex">
            {{-- Sidebar --}}
            <x-layouts.dashboard-sidebar />

            {{-- Main content area --}}
            <div class="flex-1 flex flex-col min-w-0 lg:ml-60" :class="{ 'lg:!ml-0': !sidebarOpen }">
                {{-- Header --}}
                <x-layouts.dashboard-header />

                {{-- Page content --}}
                <main class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Mobile sidebar overlay --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden"
            @click="mobileMenuOpen = false"
        ></div>

        @livewire('notifications')

        @filamentScripts
        @vite('resources/js/app.js')

    </body>

</html>
