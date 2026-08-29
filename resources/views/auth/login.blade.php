<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — {{ config('app.name', 'Etnicos 365') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-brand-900 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-950 flex items-center justify-center p-4">
    <main class="w-full max-w-md">
        <div class="mb-6 flex flex-col items-center text-center">
            <img src="{{ asset('img/logo.jpg') }}" alt="Logo Etnicos 365"
                 class="h-20 w-20 rounded-full object-cover ring-4 ring-white/20 shadow-lg">
            <h1 class="mt-4 text-2xl font-bold text-white">Etnicos 365</h1>
            <p class="text-sm text-brand-200">Fábrica de jeans — Sistema de gestión</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-xl sm:p-8">
            <h2 class="text-lg font-bold text-gray-900">Iniciar sesión</h2>
            <p class="mb-6 text-sm text-gray-600">Ingrese sus credenciales para acceder al sistema.</p>

            @if ($errors->any())
                <div role="alert" class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-800">
                    <p class="font-medium">No se pudo iniciar sesión</p>
                    <ul class="mt-1 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-brand-700 px-4 py-2.5 font-medium text-white transition-colors hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Ingresar
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-brand-300">© {{ date('Y') }} Etnicos 365 — Fábrica de jeans</p>
    </main>
</body>
</html>