@extends('layouts.public')

@section('title', 'Catálogo - Etnicos 365')

@section('content')
    <!-- Hero -->
    <section class="relative mb-10 overflow-hidden rounded-3xl bg-gradient-to-br from-brand-950 via-brand-800 to-brand-700 px-6 py-12 text-white sm:px-12 md:py-16">
        {{-- Decoración de fondo --}}
        <div class="pointer-events-none absolute -top-24 -right-16 h-72 w-72 rounded-full bg-brand-500/30 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-brand-400/20 blur-3xl" aria-hidden="true"></div>

        <div class="relative max-w-2xl">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-100 ring-1 ring-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>
                Nueva colección
            </span>

            <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight md:text-5xl">
                Jeans con alma colombiana
            </h1>
            <p class="mt-4 max-w-xl text-lg leading-relaxed text-brand-100">
                Descubre prendas fabricadas con materiales de primera, diseñadas para durar y hacerte sentir único. Calidad y estilo en cada costura.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="#catalog"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 font-semibold text-brand-900 shadow-lg transition hover:bg-brand-50">
                    Ver catálogo
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="#catalog"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3 font-semibold text-white backdrop-blur transition hover:bg-white/20">
                    Explorar
                </a>
            </div>
        </div>

        {{-- Indicadores de confianza --}}
        <div class="relative mt-12 grid grid-cols-1 gap-4 border-t border-white/15 pt-8 sm:grid-cols-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-brand-100" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-semibold">Envío nacional</p>
                    <p class="text-xs text-brand-200">A todo el país</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-brand-100" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-semibold">Calidad garantizada</p>
                    <p class="text-xs text-brand-200">Materiales premium</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-brand-100" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-semibold">Pago seguro</p>
                    <p class="text-xs text-brand-200">PSE y más</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Catálogo -->
    <section id="catalog" class="scroll-mt-24">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Catálogo</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $products->total() }} producto{{ $products->total() !== 1 ? 's' : '' }} disponible{{ $products->total() !== 1 ? 's' : '' }}
                </p>
            </div>
        </div>

        <!-- Barra de filtros -->
        <form method="GET" action="{{ route('store.index') }}" class="mb-8 space-y-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                {{-- Búsqueda --}}
                <div class="relative w-full lg:max-w-sm">
                    <label for="search" class="sr-only">Buscar productos</label>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="search"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Buscar por nombre, código, modelo o categoría..."
                           class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-11 pr-4 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-brand-400 focus:ring-2 focus:ring-brand-100">
                </div>

                {{-- Orden --}}
                <div class="flex items-center gap-2">
                    <label for="sort" class="text-sm text-gray-500 whitespace-nowrap">Ordenar por</label>
                    <select id="sort" name="sort" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-100">
                        <option value="">Destacados</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Precio: menor a mayor</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Precio: mayor a menor</option>
                        <option value="newest" @selected(request('sort') === 'newest')>Más recientes</option>
                    </select>
                </div>
            </div>

            {{-- Chips de categoría --}}
            @if ($categories->isNotEmpty())
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" name="category" value=""
                            class="rounded-full px-4 py-2 text-sm font-medium transition {{ ! request('category') ? 'bg-brand-700 text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-600 hover:border-brand-300 hover:text-brand-700' }}">
                        Todos
                    </button>
                    @foreach ($categories as $category)
                        <button type="submit" name="category" value="{{ $category }}"
                                class="rounded-full px-4 py-2 text-sm font-medium transition {{ request('category') === $category ? 'bg-brand-700 text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-600 hover:border-brand-300 hover:text-brand-700' }}">
                            {{ $category }}
                        </button>
                    @endforeach

                    @if (request('category') || request('search') || request('sort'))
                        <a href="{{ route('store.index') }}"
                           class="ml-auto inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-brand-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Limpiar filtros
                        </a>
                    @endif
                </div>
            @endif
        </form>

        {{-- Grid de productos --}}
        @forelse ($products as $product)
            @if ($loop->first)
                <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-3 xl:grid-cols-4">
            @endif

            <article class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                <a href="{{ route('store.show', $product) }}" class="relative block aspect-[3/4] overflow-hidden bg-gray-100">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             loading="lazy"
                             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-gray-100 text-brand-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-14 w-14" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                    @endif

                    @if ($product->stock_qty > 0 && $product->stock_qty <= 5)
                        <span class="absolute left-2 top-2 rounded-full bg-yellow-500 px-2.5 py-1 text-[11px] font-semibold text-white shadow">
                            ¡Pocas unidades!
                        </span>
                    @endif
                </a>

                <div class="flex flex-1 flex-col p-3 sm:p-4">
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <span class="truncate text-[11px] font-medium uppercase tracking-wide text-gray-400">{{ $product->code }}</span>
                        @if ($product->category)
                            <span class="shrink-0 rounded-md bg-brand-50 px-1.5 py-0.5 text-[10px] font-semibold text-brand-700">{{ $product->category }}</span>
                        @endif
                    </div>

                    <a href="{{ route('store.show', $product) }}" class="line-clamp-2 text-sm font-semibold text-gray-900 hover:text-brand-700">
                        {{ $product->name }}
                    </a>

                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        @if ($product->model)
                            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[11px] text-gray-600">{{ $product->model }}</span>
                        @endif
                        @if ($product->size)
                            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[11px] text-gray-600">{{ $product->size }}</span>
                        @endif
                        @if ($product->color)
                            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[11px] text-gray-600">{{ $product->color }}</span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-2 border-t border-gray-100 pt-3">
                        <span class="text-base font-bold text-brand-900 sm:text-lg">${{ number_format($product->price, 0, ',', '.') }}</span>
                        <form method="POST" action="{{ route('store.cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-800"
                                    aria-label="Agregar {{ $product->name }} al carrito">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                </svg>
                                Agregar
                            </button>
                        </form>
                    </div>
                </div>
            </article>

            @if ($loop->last)
                </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white py-20 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <h3 class="mt-6 text-xl font-bold text-gray-900">No hay productos disponibles</h3>
                <p class="mt-2 max-w-sm text-gray-500">No se encontraron productos que coincidan con tu búsqueda o filtros.</p>
                <a href="{{ route('store.index') }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-xl bg-brand-700 px-6 py-3 font-semibold text-white transition hover:bg-brand-800">
                    Ver todo el catálogo
                </a>
            </div>
        @endforelse

        {{-- Paginación --}}
        @if ($products->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $products->links('pagination::tailwind') }}
            </div>
        @endif
    </section>
@endsection
