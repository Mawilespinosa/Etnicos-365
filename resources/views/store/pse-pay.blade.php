@extends('layouts.public')

@section('title', 'Pago PSE - Etnicos 365')

@section('content')
    <div class="max-w-2xl mx-auto text-center py-12">
        <!-- Step indicator -->
        <div class="flex items-center justify-center gap-4 mb-10">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold">1</div>
                <span class="hidden sm:inline text-sm font-medium text-green-600">Carrito</span>
            </div>
            <div class="hidden sm:block w-16 h-0.5 bg-green-600"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold">2</div>
                <span class="hidden sm:inline text-sm font-medium text-green-600">Checkout</span>
            </div>
            <div class="hidden sm:block w-16 h-0.5 bg-green-600"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-brand-700 text-white flex items-center justify-center font-bold">3</div>
                <span class="hidden sm:inline text-sm font-medium text-brand-700">Pago PSE</span>
            </div>
            <div class="hidden sm:block w-16 h-0.5 bg-gray-300"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold">4</div>
                <span class="hidden sm:inline text-sm font-medium text-gray-500">Confirmación</span>
            </div>
        </div>

        <!-- PSE Payment Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
            <div class="mb-8">
                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-green-600" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A2.25 2.25 0 013 7.5h-1.5A.75.75 0 001.5 8.25v7.5A.75.75 0 002.25 16.5h1.5a2.25 2.25 0 012.25 2.25H15a.75.75 0 00.75-.75V8.25a.75.75 0 00-.75-.75h-1.5a2.25 2.25 0 01-2.25-2.25H3.75M12 12a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Pago con PSE</h1>
                <p class="text-gray-600">Serás redirigido a la pasarela de pagos para completar tu compra de forma segura.</p>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8 text-left">
                <h2 class="font-bold text-gray-900 mb-4">Resumen del pedido</h2>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Número de orden</span>
                        <span class="font-medium text-gray-900">{{ $sale->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Fecha</span>
                        <span class="font-medium text-gray-900">{{ $sale->sale_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Productos</span>
                        <span class="font-medium text-gray-900">{{ $sale->items->sum('quantity') }} items</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Subtotal</span>
                        <span class="font-medium">${{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">IVA (19%)</span>
                        <span class="font-medium">${{ number_format($sale->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between text-lg font-bold">
                        <span>Total a pagar</span>
                        <span class="text-brand-900">${{ number_format($sale->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- PSE Button (Simulated) -->
            <form method="POST" action="{{ route('store.pse.callback') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                <button type="submit"
                        class="w-full bg-green-600 text-white font-semibold py-4 rounded-xl hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 inline mr-2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A2.25 2.25 0 013 7.5h-1.5A.75.75 0 001.5 8.25v7.5A.75.75 0 002.25 16.5h1.5a2.25 2.25 0 012.25 2.25H15a.75.75 0 00.75-.75V8.25a.75.75 0 00-.75-.75h-1.5a2.25 2.25 0 01-2.25-2.25H3.75M12 12a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                    Pagar con PSE (Simulación)
                </button>

                <a href="{{ route('store.cart') }}"
                   class="block text-center text-sm text-gray-500 hover:text-gray-700">
                    Cancelar y volver al carrito
                </a>
            </form>

            <!-- Security notice -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center gap-2 text-sm text-gray-500 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Entorno de prueba - Simulación de PSE</span>
                </div>
                <p class="text-xs text-gray-400">
                    En este entorno de desarrollo, el botón "Pagar con PSE" simula una transacción exitosa
                    y redirige directamente a la página de confirmación. No se procesa ningún pago real.
                </p>
            </div>
        </div>

        <!-- Order items detail -->
        <div class="mt-8 bg-white rounded-2xl border border-gray-200 p-6 text-left">
            <h2 class="font-bold text-gray-900 mb-4">Detalle de productos</h2>
            <div class="space-y-3">
                @foreach ($sale->items as $item)
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                            @if ($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $item->quantity }} x ${{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        </div>
                        <span class="font-medium text-gray-900">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection