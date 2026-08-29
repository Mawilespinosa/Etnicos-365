<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Etnicos 365') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-brand-900 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-950 flex items-center justify-center p-4">
    <main class="w-full max-w-lg text-center">
        <img src="{{ asset('img/logo.jpg') }}" alt="Logo Etnicos 365"
             class="mx-auto h-24 w-24 rounded-full object-cover ring-4 ring-white/20 shadow-lg">
        <h1 class="mt-6 text-3xl font-bold text-white">Etnicos 365</h1>
        <p class="mt-2 text-brand-200">Sistema de gestión para fábrica de jeans</p>
        <p class="mx-auto mt-4 max-w-md text-sm text-brand-300">
            Producción, inventario, ventas y finanzas en una sola plataforma.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="w-full rounded-lg bg-white px-5 py-2.5 font-medium text-brand-800 transition-colors hover:bg-brand-100 sm:w-auto">
                    Ir al panel
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="w-full rounded-lg bg-white px-5 py-2.5 font-medium text-brand-800 transition-colors hover:bg-brand-100 sm:w-auto">
                    Iniciar sesión
                </a>
            @endauth
        </div>
        <p class="mt-8 text-xs text-brand-300">© {{ date('Y') }} Etnicos 365 — Fábrica de jeans</p>
    </main>
</body>
</html>