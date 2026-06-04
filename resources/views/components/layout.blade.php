<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' — ' : '' }}{{ config('idcgames-ui.app_name', config('app.name')) }}</title>

    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

    {{-- Vite assets del proyecto hijo (cada proyecto compila su propio CSS/JS) --}}
    @isset($head)
        {{ $head }}
    @endisset

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Inertia head --}}
    @inertiaHead
</head>
<body class="h-full bg-idc-dark text-idc-light antialiased {{ $bodyClass }}">

    @if($withNavbar)
        <x-idcgames::navbar :active="$attributes->get('active', '')" />
    @endif

    <main class="{{ $withNavbar ? 'pt-16' : '' }}">
        {{ $slot }}
    </main>

    @if($withFooter)
        <x-idcgames::footer />
    @endif

    {{-- Scripts extra del proyecto hijo --}}
    @isset($scripts)
        {{ $scripts }}
    @endisset

    @inertia

</body>
</html>
