@extends('layouts.app')

@section('title', 'Nueva venta')

@section('content')
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
                                <select :name="`items[${index}][product_id]`" x-model="line.product_id"
                                        @change="line.unit_price = $event.target.selectedOptions[0]?.dataset.price || 0"
                                        required
                                        class="w-full rounded border border-gray-300 px-3 py-2">
                                    <option value="">— Producto —</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                            {{ $product->code }} — {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
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
    </form>

    <script>
        function saleForm() {
            return {
                lines: @json(old('items') ?? [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]]),
                discount: {{ (float) old('discount', 0) }},
                paymentAmount: {{ (float) old('payment_amount', 0) }},
                taxRate: {{ config('sales.tax_rate') }},
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