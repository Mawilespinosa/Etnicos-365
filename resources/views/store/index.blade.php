@extends('layouts.public')

@section('title', 'Catálogo - Etnicos 365')

@section('content')
    <!-- Hero Section -->
    <section class="mb-12">
        <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-brand-700 rounded-2xl p-8 md:p-12 text-white">
            <div class="max-w-3xl">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">Jeans con alma colombiana</h1>
                <p class="text-lg md:text-xl text-brand-100 mb-6">
                    Descubre nuestra colección de jeans fabricados con los mejores materiales,
                    diseñados para durar y hacerte sentir único.
                </p>
                <a href="#products"
                   class="inline-flex items-center gap-2 bg-white text-brand-900 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    Ver catálogo
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Search and Filters -->
    <div class="mb-8">
        <form method="GET" action="{{ route('store.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <label for="search" class="sr-only">Buscar productos</label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Buscar por nombre, código, modelo o categoría..."
                       class="w-full rounded-lg border border-gray-300 px-4 py-3 pl-10 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            @if (request('search'))
                <a href="{{ route('store.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Products Grid -->
    <div id="products" class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">{{ $products->total() }} producto{{ $products->total() !== 1 ? 's' : '' }} encontrado{{ $products->total() !== 1 ? 's' : '' }}</h2>
        </div>

        @forelse ($products as $product)
            @if ($loop->first)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @endif

            <article class="group bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
                <div class="relative aspect-square bg-gray-100 overflow-hidden">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 w-12" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                    @endif

                    @if ($product->stock_qty > 0 && $product->stock_qty <= 5)
                        <span class="absolute top-2 right-2 bg-yellow-600 text-white text-xs font-medium px-2 py-1 rounded-full">
                            ¡Pocas unidades!
                        </span>
                    @endif
                </div>

                <div class="p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="font-semibold text-gray-900 line-clamp-1">{{ $product->name }}</h3>
                        <span class="text-sm text-gray-500 whitespace-nowrap">{{ $product->code }}</span>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                        @if ($product->model)
                            <span class="px-2 py-0.5 bg-gray-100 rounded">{{ $product->model }}</span>
                        @endif
                        @if ($product->size)
                            <span class="px-2 py-0.5 bg-gray-100 rounded">{{ $product->size }}</span>
                        @endif
                        @if ($product->color)
                            <span class="px-2 py-0.5 bg-gray-100 rounded">{{ $product->color }}</span>
                        @endif
                    </div>

                    <div class="flex items-baseline justify-between">
                        <div>
                            <span class="text-2xl font-bold text-brand-900">${{ number_format($product->price, 0, ',', '.') }}</span>
                            @if ($product->stock_qty > 0)
                                <span class="ml-2 text-xs text-green-600 font-medium">Stock: {{ $product->stock_qty }}</span>
                            @endif
                        </div>
                        <a href="{{ route('store.show', $product) }}"
                           class="inline-flex items-center gap-1 text-sm font-medium text-brand-700 hover:text-brand-900">
                            Ver detalles
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </article>

            @if ($loop->last)
                </div>
            @endif
        @empty
            <div class="text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-16 w-16 mx-auto text-gray-300 mb-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay productos disponibles</h3>
                <p class="text-gray-500 mb-6">No se encontraron productos que coincidan con tu búsqueda.</p>
                @if (request('search'))
                    <a href="{{ route('store.index') }}"
                       class="inline-flex items-center gap-2 bg-brand-700 text-white px-6 py-3 rounded-lg hover:bg-brand-800">
                        Ver todo el catálogo
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($products->hasPages())
        <div class="flex justify-center">
            {{ $products->links('pagination::tailwind') }}
        </div>
    @endif
@endsection