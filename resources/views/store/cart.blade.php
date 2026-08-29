@extends('layouts.public')

@section('title', 'Carrito - Etnicos 365')

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500" aria-label="Ruta de navegación">
            <li><a href="{{ route('store.index') }}" class="hover:text-brand-700">Inicio</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></li>
            <li class="text-gray-900 font-medium" aria-current="page">Carrito</li>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Tu carrito</h1>
    </div>

    @if (empty($items))
        <div class="text-center py-16">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-16 w-16 mx-auto text-gray-300 mb-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tu carrito está vacío</h2>
            <p class="text-gray-500 mb-6">Agrega algunos productos para comenzar tu compra.</p>
            <a href="{{ route('store.index') }}"
               class="inline-flex items-center gap-2 bg-brand-700 text-white px-6 py-3 rounded-lg hover:bg-brand-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m-16.5 0l-1.28 8.563a1.125 1.125 0 001.12 1.244h9.664a1.125 1.125 0 001.12-1.244L20.25 3H5.25" />
                </svg>
                Seguir comprando
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('store.cart.update') }}" class="space-y-6">
            @csrf

            <!-- Cart Items -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio unitario</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                                @if ($item['product']->image)
                                                    <img src="{{ asset('storage/' . $item['product']->image) }}"
                                                         alt="{{ $item['product']->name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-gray-900">{{ $item['product']->name }}</h3>
                                                <p class="text-sm text-gray-500">{{ $item['product']->code }}</p>
                                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                                    @if ($item['product']->model)
                                                        <span class="px-1.5 py-0.5 bg-gray-100 rounded">{{ $item['product']->model }}</span>
                                                    @endif
                                                    @if ($item['product']->size)
                                                        <span class="px-1.5 py-0.5 bg-gray-100 rounded">{{ $item['product']->size }}</span>
                                                    @endif
                                                    @if ($item['product']->color)
                                                        <span class="px-1.5 py-0.5 bg-gray-100 rounded">{{ $item['product']->color }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-900 font-medium">
                                        ${{ number_format($item['product']->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number"
                                               name="items[{{ $index }}][quantity]"
                                               value="{{ $item['quantity'] }}"
                                               min="0"
                                               max="{{ $item['product']->stock_qty }}"
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                               required>
                                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product']->id }}">
                                        <p class="text-xs text-gray-500 mt-1">Máx: {{ $item['product']->stock_qty }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-gray-900 font-semibold">
                                        ${{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <button type="submit"
                                                name="items[{{ $index }}][quantity]"
                                                value="0"
                                                class="text-red-600 hover:text-red-800 font-medium text-sm"
                                                onclick="return confirm('¿Eliminar este producto del carrito?')">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-gray-700 hover:text-brand-700 font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.672a45.166 45.166 0 0011.591-7.243m-11.59 7.243c.307-.798.86-1.504 1.57-2.021m-11.59 7.243v4.672m0 0h.008v.008H2.985v-.008zM12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Actualizar carrito
                    </button>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:sticky lg:top-24">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Resumen del pedido</h2>

                <dl class="space-y-3 text-gray-700">
                    <div class="flex justify-between">
                        <dt>Subtotal ({{ collect($items)->sum('quantity') }} productos)</dt>
                        <dd class="font-medium">${{ number_format($subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>IVA ({{ (config('sales.tax_rate') * 100) }}%)</dt>
                        <dd class="font-medium">${{ number_format($tax, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <dt>Total</dt>
                        <dd>${{ number_format($total, 0, ',', '.') }}</dd>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 space-y-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-green-600" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Envío gratis en compras > $200.000</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-green-600" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pago seguro con PSE</span>
                    </div>
                </div>

                <a href="{{ route('store.checkout') }}"
                   class="mt-6 w-full bg-brand-700 text-white font-semibold py-4 rounded-xl hover:bg-brand-800 transition-colors text-center block focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Proceder al pago
                </a>

                <p class="mt-4 text-center text-sm text-gray-500">
                    <a href="{{ route('store.index') }}" class="text-brand-700 hover:underline">← Seguir comprando</a>
                </p>
            </div>
        </form>
    @endif
@endsection