@extends('layouts.app')

@section('title', 'Factura ' . $sale->invoice_number)

@section('content')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-area {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
            body {
                background: white !important;
            }
            main {
                padding: 0 !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
            }
            thead {
                display: table-header-group;
            }
        }
    </style>

    @php
        $statusBadge = match ($sale->status) {
            'draft' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
        $statusLabel = match ($sale->status) {
            'draft' => 'Borrador',
            'confirmed' => 'Confirmada',
            'cancelled' => 'Cancelada',
            default => $sale->status,
        };
        $paymentLabel = match ($sale->payment_status) {
            'pending' => 'Pendiente',
            'partial' => 'Parcial',
            'paid' => 'Pagada',
            default => $sale->payment_status,
        };
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 no-print">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold">Factura {{ $sale->invoice_number }}</h1>
            <span class="inline-block rounded px-2 py-1 text-xs font-medium {{ $statusBadge }}">{{ $statusLabel }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if (auth()->user()->hasPermission('sales.update') && $sale->status === 'draft')
                <form method="POST" action="{{ route('sales.confirm', $sale) }}"
                      onsubmit="return confirm('¿Confirmar esta venta? Se descontará inventario y se registrará el ingreso.')">
                    @csrf
                    <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Confirmar venta</button>
                </form>
            @endif
            @if (auth()->user()->hasPermission('sales.update') && $sale->status === 'confirmed')
                <form method="POST" action="{{ route('sales.cancel', $sale) }}"
                      onsubmit="return confirm('¿Cancelar esta venta? Se devolverá el inventario y se anulará el ingreso.')">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white rounded px-4 py-2 hover:bg-red-700">Cancelar venta</button>
                </form>
            @endif
            @if (auth()->user()->hasPermission('sales.delete') && $sale->status === 'draft')
                <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                      onsubmit="return confirm('¿Eliminar esta venta en borrador?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                </form>
            @endif
            <button onclick="window.print()" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Imprimir</button>
            <a href="{{ route('sales.index') }}" class="text-gray-600 hover:underline">Volver</a>
        </div>
    </div>

    <div class="bg-white rounded shadow p-8 print-area">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b pb-6 mb-6">
            <div>
                <h2 class="text-2xl font-bold">Etnicos 365</h2>
                <p class="text-sm text-gray-600">Fábrica de jeans</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold">FACTURA {{ $sale->invoice_number }}</p>
                <p class="text-sm text-gray-600">Fecha: {{ $sale->sale_date->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-600">Estado: {{ $statusLabel }} · Pago: {{ $paymentLabel }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Cliente</h3>
                <p class="font-medium">{{ $sale->client->name }}</p>
                <p class="text-sm text-gray-600">{{ $sale->client->document_type }} {{ $sale->client->document_number }}</p>
                <p class="text-sm text-gray-600">{{ $sale->client->address }}, {{ $sale->client->city }}</p>
                <p class="text-sm text-gray-600">{{ $sale->client->phone }}</p>
            </div>
            <div class="md:text-right">
                <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Vendedor</h3>
                <p class="font-medium">{{ $sale->seller?->name ?? '—' }}</p>
                @if ($sale->notes)
                    <h3 class="text-xs font-medium text-gray-500 uppercase mt-4 mb-1">Notas</h3>
                    <p class="text-sm text-gray-600">{{ $sale->notes }}</p>
                @endif
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200 mb-6">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio unitario</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($sale->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->product->name }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-full max-w-xs space-y-1">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>{{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Descuento</span>
                    <span>{{ number_format($sale->discount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>IVA ({{ config('sales.tax_rate') * 100 }}%)</span>
                    <span>{{ number_format($sale->tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Total</span>
                    <span>{{ number_format($sale->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t pt-6">
            <h3 class="text-lg font-bold mb-3">Pagos realizados</h3>

            @if ($sale->payments->isNotEmpty())
                <table class="min-w-full divide-y divide-gray-200 mb-4">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registrado por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($sale->payments as $payment)
                            @php
                                $methodLabel = match ($payment->method) {
                                    'cash' => 'Efectivo',
                                    'transfer' => 'Transferencia',
                                    'card' => 'Tarjeta',
                                    'check' => 'Cheque',
                                    default => $payment->method,
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $methodLabel }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $payment->user?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-500 mb-4">No se han registrado pagos.</p>
            @endif

            <div class="flex justify-end">
                <div class="w-full max-w-xs space-y-1">
                    <div class="flex justify-between">
                        <span>Total</span>
                        <span>{{ number_format($sale->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pagado</span>
                        <span>{{ number_format($sale->payments->sum('amount'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span>Saldo pendiente</span>
                        <span>{{ number_format($sale->balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if (auth()->user()->hasPermission('sales.update') && $sale->status !== 'cancelled' && $sale->balance > 0)
                <form method="POST" action="{{ route('sales.payments.store', $sale) }}"
                      class="mt-6 bg-gray-50 rounded p-4 no-print">
                    @csrf
                    <h4 class="font-bold mb-3">Registrar pago</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label for="amount" class="block text-sm font-medium mb-1">Monto</label>
                            <input id="amount" type="number" step="0.01" min="0.01" name="amount" required
                                   class="w-full rounded border border-gray-300 px-3 py-2">
                            @error('amount')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="payment_date" class="block text-sm font-medium mb-1">Fecha</label>
                            <input id="payment_date" type="date" name="payment_date"
                                   value="{{ now()->toDateString() }}" required
                                   class="w-full rounded border border-gray-300 px-3 py-2">
                            @error('payment_date')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="method" class="block text-sm font-medium mb-1">Método</label>
                            <select id="method" name="method"
                                    class="w-full rounded border border-gray-300 px-3 py-2">
                                <option value="cash">Efectivo</option>
                                <option value="transfer">Transferencia</option>
                                <option value="card">Tarjeta</option>
                                <option value="check">Cheque</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800 w-full">
                                Registrar pago
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="notes" class="block text-sm font-medium mb-1">Notas</label>
                        <input id="notes" type="text" name="notes"
                               class="w-full rounded border border-gray-300 px-3 py-2">
                        @error('notes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection