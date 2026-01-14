<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="/favicon.ico" sizes="any">

        <title>{{ $title ?? config('app.name') . ' - ' . __('Marketing Dashboard') }}</title>
        <meta name="description" content="{{ $description ?? __('Gyűjtse össze weboldalának marketing adatait egyetlen dashboardon. Google Analytics, Search Console és Google Ads integráció, automatikus KPI-követés és riportok.') }}">

        {{-- Open Graph --}}
        <meta property="og:title" content="{{ $ogTitle ?? config('app.name') . ' – Marketing dashboard' }}">
        <meta property="og:description" content="{{ $ogDescription ?? __('GA4, Search Console, Google Ads – egy helyen. Automatikus KPI-ok és riportok.') }}">
        <meta property="og:image" content="{{ $ogImage ?? asset('images/kontrolling-og-image.png') }}">

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
        @vite('resources/js/app.js')

    </head>

    <body class="antialiased">
        {{ $slot }}

        @livewire('notifications')

        @filamentScripts

    </body>

</html>
