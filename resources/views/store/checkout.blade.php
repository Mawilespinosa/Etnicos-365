@extends('layouts.public')

@section('title', 'Checkout - Etnicos 365')

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500" aria-label="Ruta de navegación">
            <li><a href="{{ route('store.index') }}" class="hover:text-brand-700">Inicio</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></li>
            <li><a href="{{ route('store.cart') }}" class="hover:text-brand-700">Carrito</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></li>
            <li class="text-gray-900 font-medium" aria-current="page">Checkout</li>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Finalizar compra</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('store.checkout.process') }}" class="space-y-6">
                @csrf

                <!-- Contact Information -->
                <section class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-brand-700" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 00-1.07 1.916V17.25A2.25 2.25 0 004.5 19.5h15a2.25 2.25 0 002.25-2.25V8.91a2.25 2.25 0 00-1.07-1.916l-7.5-4.615a2.25 2.25 0 01-2.36 0L21.75 6.75z" />
                        </svg>
                        Información de contacto
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                   autocomplete="name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                   autocomplete="email">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                   autocomplete="tel"
                                   placeholder="+57 3XX XXX XXXX">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Shipping Address -->
                <section class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-brand-700" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                        </svg>
                        Dirección de envío
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Dirección completa <span class="text-red-500">*</span></label>
                            <textarea id="address"
                                      name="address"
                                      rows="3"
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                      placeholder="Calle, número, apartamento, barrio, referencias...">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ciudad <span class="text-red-500">*</span></label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   value="{{ old('city') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                   autocomplete="address-level2"
                                   placeholder="Bogotá, Medellín, Cali, etc.">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas del pedido (opcional)</label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="2"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                      placeholder="Instrucciones de entrega, horario preferido, etc.">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Payment Method -->
                <section class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-brand-700" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m-16.5 0l-1.28 8.563a1.125 1.125 0 001.12 1.244h9.664a1.125 1.125 0 001.12-1.244L20.25 3H5.25" />
                        </svg>
                        Método de pago
                    </h2>

                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-green-600" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A2.25 2.25 0 013 7.5h-1.5A.75.75 0 001.5 8.25v7.5A.75.75 0 002.25 16.5h1.5a2.25 2.25 0 012.25 2.25H15a.75.75 0 00.75-.75V8.25a.75.75 0 00-.75-.75h-1.5a2.25 2.25 0 01-2.25-2.25H3.75M12 12a3 3 0 100-6 3 3 0 000 6z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">PSE (Pago Seguro en Línea)</h3>
                                <p class="text-sm text-gray-500">Paga directamente desde tu cuenta bancaria de forma segura</p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-600">
                            Serás redirigido a la pasarela de PSE para completar el pago. Una vez confirmado, recibirás la confirmación de tu pedido por email.
                        </p>
                    </div>
                </section>

                <!-- Submit Button -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24">
                    <button type="submit"
                            class="w-full bg-brand-700 text-white font-semibold py-4 rounded-xl hover:bg-brand-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 inline mr-2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m-16.5 0l-1.28 8.563a1.125 1.125 0 001.12 1.244h9.664a1.125 1.125 0 001.12-1.244L20.25 3H5.25" />
                        </svg>
                        Confirmar pedido y pagar con PSE
                    </button>

                    <p class="mt-3 text-center text-sm text-gray-500">
                        <a href="{{ route('store.cart') }}" class="text-brand-700 hover:underline">← Volver al carrito</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Resumen del pedido</h2>

                <div class="space-y-3 mb-4">
                    @foreach ($items as $item)
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                @if ($item['product']->image)
                                    <img src="{{ asset('storage/' . $item['product']->image) }}"
                                         alt="{{ $item['product']->name }}"
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
                                <p class="font-medium text-gray-900 truncate">{{ $item['product']->name }}</p>
                                <p class="text-sm text-gray-500">{{ $item['quantity'] }} x ${{ number_format($item['product']->price, 0, ',', '.') }}</p>
                            </div>
                            <span class="font-medium text-gray-900 whitespace-nowrap">${{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 pt-4 space-y-3">
                    <div class="flex justify-between text-gray-700">
                        <span>Subtotal</span>
                        <span class="font-medium">${{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>IVA ({{ (config('sales.tax_rate') * 100) }}%)</span>
                        <span class="font-medium">${{ number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-600 space-y-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-green-600" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Envío gratis > $200.000</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-green-600" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pago seguro PSE</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-green-600" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>30 días para cambios</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection