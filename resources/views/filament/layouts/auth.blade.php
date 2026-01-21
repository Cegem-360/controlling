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

<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <!-- Header with logo -->
    <header class="bg-white border-b border-gray-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('home') }}">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10">
                </a>
                <x-language-switcher />
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="flex min-h-[calc(100vh-65px)] items-center justify-center p-4">
        {{ $slot }}
    </main>

    @filamentScripts
    @vite('resources/js/app.js')
</body>

</html>
