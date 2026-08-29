@extends('layouts.public')

@section('title', $product->name . ' - Etnicos 365')

@section('content')
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Ruta de navegación">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('store.index') }}" class="hover:text-brand-700">Inicio</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></li>
            <li><a href="{{ route('store.index') }}" class="hover:text-brand-700">Catálogo</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></li>
            <li class="text-gray-900 font-medium truncate max-w-xs" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Image Gallery -->
        <div class="lg:sticky lg:top-24">
            <div class="relative aspect-square bg-gray-100 rounded-2xl overflow-hidden mb-4">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover"
                         id="main-image">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-24 w-24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                @endif

                @if ($product->stock_qty > 0 && $product->stock_qty <= 5)
                    <span class="absolute top-4 right-4 bg-yellow-600 text-white text-sm font-medium px-3 py-1 rounded-full">
                        ¡Solo {{ $product->stock_qty }} unidades!
                    </span>
                @elseif ($product->stock_qty <= 0)
                    <span class="absolute top-4 right-4 bg-red-600 text-white text-sm font-medium px-3 py-1 rounded-full">
                        Agotado
                    </span>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div>
            <header class="mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm font-medium text-gray-500">{{ $product->code }}</span>
                    @if ($product->category)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-sm rounded">{{ $product->category }}</span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $product->name }}</h1>
            </header>

            @if ($product->description)
                <div class="prose prose-gray mb-6 max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Specs -->
            <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
                @if ($product->model)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Modelo</dt>
                        <dd class="mt-1 font-medium">{{ $product->model }}</dd>
                    </div>
                @endif
                @if ($product->size)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Talla</dt>
                        <dd class="mt-1 font-medium">{{ $product->size }}</dd>
                    </div>
                @endif
                @if ($product->color)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Color</dt>
                        <dd class="mt-1 font-medium">{{ $product->color }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Disponibilidad</dt>
                    <dd class="mt-1 font-medium {{ $product->stock_qty > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock_qty > 0 ? 'En stock (' . $product->stock_qty . ' unidades)' : 'Agotado' }}
                    </dd>
                </div>
            </div>

            <!-- Price and Add to Cart -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 sticky top-24">
                <div class="mb-6">
                    <span class="text-4xl font-bold text-brand-900">${{ number_format($product->price, 0, ',', '.') }}</span>
                    <p class="text-sm text-gray-500 mt-1">IVA incluido (19%)</p>
                </div>

                @if ($product->stock_qty > 0 && $product->is_active)
                    <form method="POST" action="{{ route('store.cart.add') }}" class="space-y-4" x-data="{ quantity: 1 }">
                        @csrf

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                            <div class="flex items-center gap-3">
                                <button type="button"
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        class="w-12 h-12 rounded-lg border border-gray-300 bg-white flex items-center justify-center text-gray-700 hover:bg-gray-50 transition-colors"
                                        aria-label="Disminuir cantidad">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                    </svg>
                                </button>
                                <input type="number"
                                       id="quantity"
                                       name="quantity"
                                       x-model.number="quantity"
                                       :min="1"
                                       :max="{{ $product->stock_qty }}"
                                       value="1"
                                       class="flex-1 w-24 h-12 text-center text-lg font-medium border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                       required>
                                <button type="button"
                                        @click="quantity = Math.min({{ $product->stock_qty }}, quantity + 1)"
                                        class="w-12 h-12 rounded-lg border border-gray-300 bg-white flex items-center justify-center text-gray-700 hover:bg-gray-50 transition-colors"
                                        aria-label="Aumentar cantidad">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <p class="text-xs text-gray-500 mt-1">Máximo {{ $product->stock_qty }} unidades disponibles</p>
                        </div>

                        <button type="submit"
                                class="w-full bg-brand-700 text-white font-semibold py-4 rounded-xl hover:bg-brand-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 inline mr-2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                            Agregar al carrito
                        </button>
                    </form>
                @else
                    <div class="text-center py-4">
                        @if (! $product->is_active)
                            <p class="text-gray-500 mb-4">Este producto no está disponible actualmente.</p>
                        @else
                            <p class="text-red-600 font-medium mb-4">Producto agotado</p>
                        @endif
                        <a href="{{ route('store.index') }}"
                           class="inline-flex items-center gap-2 text-brand-700 hover:text-brand-900 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Seguir comprando
                        </a>
                    </div>
                @endif

                <!-- Trust badges -->
                <div class="mt-8 pt-6 border-t border-gray-200 space-y-3">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-green-600 shrink-0" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Envío gratis en compras > $200.000</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-green-600 shrink-0" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Cambios y devoluciones en 30 días</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-green-600 shrink-0" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pago seguro con PSE</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products (optional) -->
    @php
        $relatedProducts = \App\Models\Product::where('is_active', true)
            ->where('stock_qty', '>', 0)
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('category', $product->category)
                  ->orWhere('model', $product->model);
            })
            ->inRandomOrder()
            ->limit(4)
            ->get();
    @endphp

    @if ($relatedProducts->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-2xl font-bold mb-6">También te puede interesar</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($relatedProducts as $related)
                    <article class="group bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
                        <div class="relative aspect-square bg-gray-100 overflow-hidden">
                            @if ($related->image)
                                <img src="{{ asset('storage/' . $related->image) }}"
                                     alt="{{ $related->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 w-12" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 line-clamp-1 mb-1">{{ $related->name }}</h3>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xl font-bold text-brand-900">${{ number_format($related->price, 0, ',', '.') }}</span>
                                <a href="{{ route('store.show', $related) }}"
                                   class="text-sm text-brand-700 hover:text-brand-900 font-medium">Ver</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection