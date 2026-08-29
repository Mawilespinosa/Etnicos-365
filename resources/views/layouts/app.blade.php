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
<body class="bg-gray-100 text-gray-900 antialiased">
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white">
        Saltar al contenido
    </a>

    @php
        $user = auth()->user();
        $userRoles = $user->roles->pluck('display_name')->join(', ');
        $navGroups = [
            'Principal' => [
                ['label' => 'Dashboard', 'permission' => 'dashboard.view', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'home'],
            ],
            'Administración' => [
                ['label' => 'Usuarios', 'permission' => 'users.view', 'route' => 'users.index', 'active' => 'users.*', 'icon' => 'users'],
                ['label' => 'Roles', 'permission' => 'roles.view', 'route' => 'roles.index', 'active' => 'roles.*', 'icon' => 'shield'],
            ],
            'Catálogos' => [
                ['label' => 'Vendedores', 'permission' => 'sellers.view', 'route' => 'sellers.index', 'active' => 'sellers.*', 'icon' => 'user-group'],
                ['label' => 'Clientes', 'permission' => 'clients.view', 'route' => 'clients.index', 'active' => 'clients.*', 'icon' => 'user'],
                ['label' => 'Proveedores', 'permission' => 'suppliers.view', 'route' => 'suppliers.index', 'active' => 'suppliers.*', 'icon' => 'truck'],
                ['label' => 'Productos', 'permission' => 'products.view', 'route' => 'products.index', 'active' => 'products.*', 'icon' => 'tag'],
                ['label' => 'Materias primas', 'permission' => 'raw_materials.view', 'route' => 'raw-materials.index', 'active' => 'raw-materials.*', 'icon' => 'cube'],
            ],
            'Producción' => [
                ['label' => 'Órdenes de producción', 'permission' => 'production.view', 'route' => 'production.orders.index', 'active' => 'production.orders.*', 'icon' => 'cog'],
            ],
            'Inventario' => [
                ['label' => 'Inventario', 'permission' => 'inventory.view', 'route' => 'inventory.index', 'active' => 'inventory.index', 'icon' => 'archive'],
                ['label' => 'Movimientos', 'permission' => 'inventory.movements', 'route' => 'inventory.movements', 'active' => 'inventory.movements', 'icon' => 'arrows'],
                ['label' => 'Alertas de stock', 'permission' => 'inventory.view', 'route' => 'inventory.alerts', 'active' => 'inventory.alerts', 'icon' => 'bell'],
            ],
            'Ventas' => [
                ['label' => 'Ventas', 'permission' => 'sales.view', 'route' => 'sales.index', 'active' => 'sales.*', 'icon' => 'cart'],
            ],
            'Finanzas' => [
                ['label' => 'Finanzas', 'permission' => 'finances.view', 'route' => 'finances.index', 'active' => 'finances.*', 'icon' => 'money'],
                ['label' => 'Reportes', 'permission' => 'reports.export', 'route' => 'reports.index', 'active' => 'reports.*', 'icon' => 'chart'],
            ],
        ];
        $icons = [
            'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />',
            'shield' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />',
            'user-group' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />',
            'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
            'truck' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />',
            'tag' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />',
            'cube' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />',
            'cog' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />',
            'archive' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />',
            'arrows' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />',
            'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />',
            'cart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />',
            'money' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'chart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
        ];
    @endphp

    <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
        <!-- Fondo oscuro al abrir el menú en móvil -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-900/60 lg:hidden" aria-hidden="true"></div>

        <!-- Sidebar -->
        <aside class="app-sidebar fixed inset-y-0 left-0 z-40 flex w-72 max-w-[85vw] flex-col bg-brand-900 text-white transition-transform duration-200 lg:static lg:z-auto lg:translate-x-0 lg:shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               aria-label="Menú principal">
            <div class="flex items-center gap-3 border-b border-brand-800 px-5 py-4">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo Etnicos 365"
                     class="h-10 w-10 rounded-full object-cover ring-2 ring-white/20">
                <div>
                    <p class="text-lg font-bold leading-tight">Etnicos 365</p>
                    <p class="text-xs text-brand-200">Fábrica de jeans</p>
                </div>
                <button type="button" @click="sidebarOpen = false" class="ml-auto rounded p-1 text-brand-200 hover:bg-brand-800 lg:hidden"
                        aria-label="Cerrar menú">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4">
                @foreach ($navGroups as $group => $items)
                    @php
                        $visible = collect($items)->filter(fn ($item) => $user->hasPermission($item['permission']));
                    @endphp
                    @if ($visible->isNotEmpty())
                        <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-brand-300 first:pt-0">{{ $group }}</p>
                        <ul class="space-y-1">
                            @foreach ($visible as $item)
                                @php
                                    $isActive = request()->routeIs($item['active']);
                                @endphp
                                <li>
                                    <a href="{{ route($item['route']) }}"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $isActive ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-800 hover:text-white' }}"
                                       @if ($isActive) aria-current="page" @endif>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                                            {!! $icons[$item['icon']] !!}
                                        </svg>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach
            </nav>

            <div class="border-t border-brand-800 px-5 py-4">
                <p class="text-sm font-medium">{{ $user->name }}</p>
                <p class="text-xs text-brand-300">{{ $userRoles ?: 'Sin rol' }}</p>
            </div>
        </aside>

        <!-- Contenido principal -->
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="app-header sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
                <div class="flex items-center justify-between gap-3 px-4 py-3 md:px-6">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="sidebarOpen = !sidebarOpen"
                                class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden"
                                aria-label="Abrir menú" aria-expanded="false" :aria-expanded="sidebarOpen.toString()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 lg:hidden">
                            <img src="{{ asset('img/logo.jpg') }}" alt="Logo Etnicos 365" class="h-8 w-8 rounded-full object-cover">
                            <span class="font-bold text-brand-900">Etnicos 365</span>
                        </a>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $userRoles ?: 'Sin rol' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                                <span class="hidden sm:inline">Cerrar sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main id="main-content" class="flex-1 p-4 md:p-6 lg:p-8">
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
        </div>
    </div>
</body>
</html>