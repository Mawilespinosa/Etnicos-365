<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Etnicos 365'))</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white">
        Saltar al contenido
    </a>

    <!-- Header -->
    <header class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
        <div class="flex items-center justify-between gap-3 px-4 py-3 md:px-6 max-w-7xl mx-auto">
            <a href="{{ route('store.index') }}" class="flex items-center gap-2" aria-label="Etnicos 365 - Inicio">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo Etnicos 365" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/20">
                <span class="font-bold text-brand-900 text-lg">Etnicos 365</span>
                <span class="text-xs text-gray-500 hidden sm:inline">Tienda oficial</span>
            </a>

            <div class="flex items-center gap-3">
                <!-- Cart link -->
                <a href="{{ route('store.cart') }}"
                   class="relative inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                   aria-label="Carrito de compras">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <span class="hidden sm:inline">Carrito</span>
                    @php
                        $cartCount = collect(session()->get('cart', []))->sum();
                    @endphp
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-brand-700 text-white text-xs font-bold">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <!-- Login link -->
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-brand-700 bg-brand-700 px-3 py-2 text-sm font-medium text-white hover:bg-brand-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    <span>Ingresar</span>
                </a>
            </div>
        </div>
    </header>

    <main id="main-content" class="max-w-7xl mx-auto px-4 py-6 md:px-6 lg:px-8">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition role="alert" aria-live="polite"
                 class="mb-4 flex items-start gap-3 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">{{ session('success') }}</div>
                <button type="button" @click="show = false" class="shrink-0 text-green-700 hover:text-green-900" aria-label="Cerrar aviso">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" role="alert" aria-live="assertive"
                 class="mb-4 flex items-start gap-3 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div class="flex-1">{{ session('error') }}</div>
                <button type="button" @click="show = false" class="shrink-0 text-red-700 hover:text-red-900" aria-label="Cerrar aviso">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
                 x-transition role="alert" aria-live="polite"
                 class="mb-4 flex items-start gap-3 rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3 text-yellow-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <div class="flex-1">{{ session('warning') }}</div>
                <button type="button" @click="show = false" class="shrink-0 text-yellow-700 hover:text-yellow-900" aria-label="Cerrar aviso">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8 md:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold text-brand-900 mb-3">Etnicos 365</h3>
                    <p class="text-gray-600 text-sm">Fábrica de jeans colombiana. Calidad, estilo y tradición en cada prenda.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-brand-900 mb-3">Contacto</h3>
                    <address class="not-italic text-gray-600 text-sm space-y-1">
                        <p>Bogotá, Colombia</p>
                        <p>Email: ventas@etnicos365.com</p>
                        <p>Tel: +57 1 234 5678</p>
                    </address>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-brand-900 mb-3">Enlaces</h3>
                    <nav class="space-y-1">
                        <a href="{{ route('store.index') }}" class="text-gray-600 text-sm hover:text-brand-700">Catálogo</a>
                        <a href="{{ route('login') }}" class="text-gray-600 text-sm hover:text-brand-700 block">Área privada</a>
                    </nav>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Etnicos 365. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>