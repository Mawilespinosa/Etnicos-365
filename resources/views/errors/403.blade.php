<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso denegado — {{ config('app.name', 'Etnicos 365') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-brand-900 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-950 flex items-center justify-center p-4">
    <main class="w-full max-w-md text-center">
        <img src="{{ asset('img/logo.jpg') }}" alt="Logo Etnicos 365"
             class="mx-auto h-20 w-20 rounded-full object-cover ring-4 ring-white/20 shadow-lg">
        <h1 class="mt-6 text-6xl font-bold text-white">403</h1>
        <p class="mt-4 text-lg text-brand-100">No tienes permiso para acceder a esta sección.</p>
        <p class="mt-2 text-sm text-brand-300">Si crees que esto es un error, contacta al administrador del sistema.</p>
        <a href="{{ url('/') }}"
           class="mt-8 inline-block rounded-lg bg-white px-5 py-2.5 font-medium text-brand-800 transition-colors hover:bg-brand-100">
            Volver al inicio
        </a>
    </main>
</body>
</html>