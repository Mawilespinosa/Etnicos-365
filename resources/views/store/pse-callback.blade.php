@extends('layouts.public')

@php
    $title = $success ? 'Pago exitoso' : 'Error en el pago';
    $step4Class = $success ? 'bg-green-600' : 'bg-red-600';
    $step4TextClass = $success ? 'text-green-600' : 'text-red-600';
@endphp

@section('title', $title . ' - Etnicos 365')

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
                <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold">3</div>
                <span class="hidden sm:inline text-sm font-medium text-green-600">Pago PSE</span>
            </div>
            <div class="hidden sm:block w-16 h-0.5 bg-green-600"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full {{ $step4Class }} text-white flex items-center justify-center font-bold">4</div>
                <span class="hidden sm:inline text-sm font-medium {{ $step4TextClass }}">Confirmación</span>
            </div>
        </div>

        @if ($success)
            <!-- Success State -->
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-green-600" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">¡Pago exitoso!</h1>
                <p class="text-gray-600 mb-6">{{ $message }}</p>

                <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6 text-left">
                    <h2 class="font-bold text-green-900 mb-4">Detalles de tu pedido</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Número de orden</span>
                            <span class="font-mono font-bold text-green-900">{{ $sale->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fecha</span>
                            <span class="font-medium">{{ $sale->sale_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total pagado</span>
                            <span class="font-bold text-green-900 text-lg">${{ number_format($sale->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Método de pago</span>
                            <span class="font-medium">PSE</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estado</span>
                            <span class="font-medium text-green-600">Pagado</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('sales.show', $sale) }}"
                       class="inline-flex items-center justify-center gap-2 w-full bg-brand-700 text-white font-semibold py-4 rounded-xl hover:bg-brand-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A2.25 2.25 0 013 7.5h-1.5A.75.75 0 001.5 8.25v7.5A.75.75 0 002.25 16.5h1.5a2.25 2.25 0 012.25 2.25H15a.75.75 0 00.75-.75V8.25a.75.75 0 00-.75-.75h-1.5a2.25 2.25 0 01-2.25-2.25H3.75M12 12a3 3 0 100-6 3 3 0 000 6z" />
                        </svg>
                        Ver factura
                    </a>

                    <a href="{{ route('store.index') }}"
                       class="inline-flex items-center justify-center gap-2 w-full border border-gray-300 text-gray-700 font-semibold py-4 rounded-xl hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m-16.5 0l-1.28 8.563a1.125 1.125 0 001.12 1.244h9.664a1.125 1.125 0 001.12-1.244L20.25 3H5.25" />
                        </svg>
                        Seguir comprando
                    </a>
                </div>

                <p class="mt-6 text-sm text-gray-500">
                    Se ha enviado la confirmación a tu email. Si no la recibes, revisa tu carpeta de spam.
                </p>
            </div>

        @else
            <!-- Error State -->
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-red-600" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Error en el pago</h1>
                <p class="text-gray-600 mb-6">{{ $message }}</p>

                <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6 text-left">
                    <h2 class="font-bold text-red-900 mb-2">¿Qué puedes hacer?</h2>
                    <ul class="space-y-2 text-sm text-red-700 text-left">
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verifica tu conexión a internet e intenta nuevamente
                        </li>
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Contacta a nuestro equipo de soporte si el problema persiste
                        </li>
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tu carrito se ha conservado para que puedas reintentar
                        </li>
                    </ul>
                </div>

                <div class="space-y-3">
                    @php
                        $retrySaleId = request()->query('sale_id');
                        $retryUrl = $retrySaleId ? route('store.pse.pay', $retrySaleId) : route('store.cart');
                    @endphp
                    <a href="{{ $retryUrl }}"
                       class="inline-flex items-center justify-center gap-2 w-full bg-brand-700 text-white font-semibold py-4 rounded-xl hover:bg-brand-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.672a45.166 45.166 0 0011.591-7.243m-11.59 7.243c.307-.798.86-1.504 1.57-2.021m-11.59 7.243v4.672m0 0h.008v.008H2.985v-.008zM12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        @if ($retrySaleId)
                            Reintentar pago
                        @else
                            Volver al carrito
                        @endif
                    </a>

                    <a href="{{ route('store.cart') }}"
                       class="inline-flex items-center justify-center gap-2 w-full border border-gray-300 text-gray-700 font-semibold py-4 rounded-xl hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m-16.5 0l-1.28 8.563a1.125 1.125 0 001.12 1.244h9.664a1.125 1.125 0 001.12-1.244L20.25 3H5.25" />
                        </svg>
                        Volver al carrito
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection