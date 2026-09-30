@extends('layouts.app')

@section('title', 'Nueva venta')

@section('content')
    @php
        $productsList = $products->map(fn ($p) => [
            'id' => $p->id,
            'code' => $p->code,
            'name' => $p->name,
            'description' => (string) $p->description,
            'image' => $p->image ? asset('storage/' . $p->image) : null,
            'price' => (float) $p->price,
        ])->values();
    @endphp

    <h1 class="text-2xl font-bold mb-6">Nueva venta</h1>

    <form method="POST" action="{{ route('sales.store') }}" class="bg-white rounded shadow p-6"
          x-data="saleForm()">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="client_id" class="block text-sm font-medium mb-1">Cliente</label>
                <select id="client_id" name="client_id" required
                        class="w-full rounded border border-gray-300 px-3 py-2">
                    <option value="">— Seleccionar cliente —</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                            {{ $client->name }} ({{ $client->document_number }})
                        </option>
                    @endforeach
                </select>
                @error('client_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="seller_id" class="block text-sm font-medium mb-1">Vendedor</label>
                <select id="seller_id" name="seller_id"
                        class="w-full rounded border border-gray-300 px-3 py-2">
                    <option value="">— Sin vendedor —</option>
                    @foreach ($sellers as $seller)
                        <option value="{{ $seller->id }}" @selected(old('seller_id') == $seller->id)>
                            {{ $seller->name }}
                        </option>
                    @endforeach
                </select>
                @error('seller_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="sale_date" class="block text-sm font-medium mb-1">Fecha</label>
                <input id="sale_date" type="date" name="sale_date"
                       value="{{ old('sale_date', now()->toDateString()) }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('sale_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <h2 class="text-lg font-bold mb-3">Detalle de la venta</h2>

        <div class="overflow-x-auto mb-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio unitario</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(line, index) in lines" :key="index">
                        <tr>
                            <td class="px-4 py-2">
                                <input type="hidden" :name="`items[${index}][product_id]`" x-model="line.product_id" required>
                                <button type="button" @click="openPicker(index)"
                                        class="flex w-full items-center justify-between gap-2 rounded border border-gray-300 bg-white px-3 py-2 text-left hover:border-gray-400">
                                    <span class="truncate" x-text="productLabel(line)" :class="line.product_id ? '' : 'text-gray-400'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0.01"
                                       :name="`items[${index}][quantity]`" x-model="line.quantity" required
                                       class="w-28 rounded border border-gray-300 px-3 py-2">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0"
                                       :name="`items[${index}][unit_price]`" x-model="line.unit_price" required
                                       class="w-40 rounded border border-gray-300 px-3 py-2">
                            </td>
                            <td class="px-4 py-2 text-right" x-text="formatMoney(lineSubtotal(line))"></td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" @click="removeLine(index)"
                                        class="text-red-600 hover:underline">Quitar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <button type="button" @click="addLine()"
                class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50 mb-6">
            + Agregar producto
        </button>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label for="discount" class="block text-sm font-medium mb-1">Descuento</label>
                    <input id="discount" type="number" step="0.01" min="0" name="discount"
                           x-model.number="discount" value="{{ old('discount', 0) }}"
                           class="w-full rounded border border-gray-300 px-3 py-2">
                    @error('discount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="payment_amount" class="block text-sm font-medium mb-1">Pago inicial</label>
                    <input id="payment_amount" type="number" step="0.01" min="0" name="payment_amount"
                           x-model.number="paymentAmount" value="{{ old('payment_amount', 0) }}"
                           class="w-full rounded border border-gray-300 px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Si es menor al total, la venta queda a crédito con saldo pendiente.</p>
                    @error('payment_amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium mb-1">Método de pago</label>
                    <select id="payment_method" name="payment_method"
                            class="w-full rounded border border-gray-300 px-3 py-2">
                        <option value="cash" @selected(old('payment_method') === 'cash')>Efectivo</option>
                        <option value="transfer" @selected(old('payment_method') === 'transfer')>Transferencia</option>
                        <option value="card" @selected(old('payment_method') === 'card')>Tarjeta</option>
                        <option value="check" @selected(old('payment_method') === 'check')>Cheque</option>
                    </select>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium mb-1">Notas</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full rounded border border-gray-300 px-3 py-2">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-gray-50 rounded p-4 space-y-2 h-fit">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span x-text="formatMoney(subtotal)"></span>
                </div>
                <div class="flex justify-between">
                    <span>Descuento</span>
                    <span x-text="formatMoney(discount)"></span>
                </div>
                <div class="flex justify-between">
                    <span>IVA ({{ config('sales.tax_rate') * 100 }}%)</span>
                    <span x-text="formatMoney(tax)"></span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Total</span>
                    <span x-text="formatMoney(total)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Saldo pendiente estimado</span>
                    <span x-text="formatMoney(Math.max(total - paymentAmount, 0))"></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Guardar venta</button>
            <a href="{{ route('sales.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>

        <div x-show="pickerOpen" x-cloak @keydown.escape.window="pickerOpen = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div x-show="pickerOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="pickerOpen = false" aria-hidden="true"></div>

            <div x-show="pickerOpen" x-cloak @click.outside="pickerOpen = false"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                 class="relative z-10 flex max-h-[92vh] w-full max-w-[44.8rem] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
                 role="dialog" aria-modal="true" aria-label="Seleccionar producto">

                <div class="flex items-start gap-4 border-b border-gray-100 px-6 py-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-lg font-bold text-gray-900">Seleccionar producto</h3>
                        <p class="text-sm text-gray-500">Elige el producto para esta línea de venta.</p>
                    </div>
                    <span class="hidden shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 sm:inline-block"
                          x-text="filteredProducts.length + ' producto' + (filteredProducts.length !== 1 ? 's' : '')"></span>
                    <button type="button" @click="pickerOpen = false"
                            class="shrink-0 rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                            aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                             class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" x-model="productSearch" placeholder="Buscar por nombre, código o descripción..."
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-11 pr-10 text-sm text-gray-900 placeholder-gray-400 transition focus:border-brand-400 focus:bg-white focus:ring-2 focus:ring-brand-100">
                        <button x-show="productSearch" x-cloak type="button" @click="productSearch = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" aria-label="Limpiar búsqueda">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto overscroll-contain bg-gray-50/70 p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="p in filteredProducts" :key="p.id">
                            <button type="button" @click="pickProduct(p)"
                                    class="group relative flex flex-col overflow-hidden rounded-xl border bg-white text-left shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                                    :class="isLineProduct(p) ? 'border-brand-600 ring-2 ring-brand-500' : 'border-gray-200 hover:border-brand-400'">
                                <div class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                    <template x-if="p.image">
                                        <img :src="p.image" :alt="p.name" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    </template>
                                    <template x-if="!p.image">
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-gray-100 text-brand-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 w-12" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                        </div>
                                    </template>

                                    <div x-show="isLineProduct(p)" x-cloak
                                         class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-brand-600 text-white shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex flex-1 flex-col p-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="line-clamp-1 font-semibold text-gray-900" x-text="p.name"></h4>
                                        <span class="whitespace-nowrap rounded-md bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500"
                                              x-text="p.code"></span>
                                    </div>
                                    <p class="mt-1 line-clamp-2 flex-1 text-xs leading-relaxed text-gray-500" x-text="p.description"></p>
                                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-2.5">
                                        <span class="text-sm font-bold text-brand-800" x-text="formatMoney(p.price)"></span>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700">
                                            <span x-show="isLineProduct(p)" x-cloak>Seleccionado</span>
                                            <span x-show="!isLineProduct(p)">Seleccionar</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                 class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <h4 class="mt-4 text-base font-semibold text-gray-900">No se encontraron productos</h4>
                        <p class="mt-1 text-sm text-gray-500">Intenta con otro término de búsqueda.</p>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 bg-white px-6 py-3">
                    <p class="text-xs text-gray-400">Haz clic en una tarjeta para seleccionar el producto.</p>
                    <button type="button" @click="pickerOpen = false"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function saleForm() {
            return {
                lines: @json(old('items') ?? [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]]),
                discount: {{ (float) old('discount', 0) }},
                paymentAmount: {{ (float) old('payment_amount', 0) }},
                taxRate: {{ config('sales.tax_rate') }},
                products: @json($productsList),
                pickerOpen: false,
                pickerIndex: null,
                productSearch: '',
                get filteredProducts() {
                    const q = this.productSearch.trim().toLowerCase();
                    if (!q) return this.products;
                    return this.products.filter(p =>
                        (p.name || '').toLowerCase().includes(q)
                        || (p.code || '').toLowerCase().includes(q)
                        || (p.description || '').toLowerCase().includes(q)
                    );
                },
                openPicker(index) {
                    this.pickerIndex = index;
                    this.productSearch = '';
                    this.pickerOpen = true;
                },
                pickProduct(p) {
                    if (this.pickerIndex !== null && this.lines[this.pickerIndex]) {
                        this.lines[this.pickerIndex].product_id = p.id;
                        this.lines[this.pickerIndex].unit_price = parseFloat(p.price) || 0;
                    }
                    this.pickerOpen = false;
                    this.pickerIndex = null;
                },
                isLineProduct(p) {
                    if (this.pickerIndex === null || !this.lines[this.pickerIndex]) return false;
                    return String(this.lines[this.pickerIndex].product_id) === String(p.id);
                },
                productLabel(line) {
                    if (!line.product_id) return '— Producto —';
                    const p = this.products.find(pr => String(pr.id) === String(line.product_id));
                    return p ? (p.code + ' — ' + p.name) : '— Producto —';
                },
                addLine() {
                    this.lines.push({ product_id: '', quantity: 1, unit_price: 0 });
                },
                removeLine(index) {
                    if (this.lines.length > 1) {
                        this.lines.splice(index, 1);
                    }
                },
                lineSubtotal(line) {
                    return (parseFloat(line.quantity) || 0) * (parseFloat(line.unit_price) || 0);
                },
                get subtotal() {
                    return this.lines.reduce((sum, line) => sum + this.lineSubtotal(line), 0);
                },
                get tax() {
                    return Math.max(this.subtotal - this.discount, 0) * this.taxRate;
                },
                get total() {
                    const taxable = Math.max(this.subtotal - this.discount, 0);
                    return taxable + this.tax;
                },
                formatMoney(value) {
                    return new Intl.NumberFormat('es-CO', {
                        style: 'currency',
                        currency: 'COP',
                        minimumFractionDigits: 0,
                    }).format(value || 0);
                },
            };
        }
    </script>
@endsection