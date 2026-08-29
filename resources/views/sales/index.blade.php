@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Ventas</h1>
        @if (auth()->user()->hasPermission('sales.create'))
            <a href="{{ route('sales.create') }}"
               class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                Nueva venta
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('sales.index') }}" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" aria-label="Buscar" placeholder="Buscar por factura o cliente"
               class="w-full max-w-md rounded border border-gray-300 px-3 py-2">
        <select name="status" aria-label="Filtrar por estado" class="rounded border border-gray-300 px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="draft" @selected(request('status') === 'draft')>Borrador</option>
            <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmada</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelada</option>
        </select>
        <select name="payment_status" aria-label="Filtrar por pago" class="rounded border border-gray-300 px-3 py-2">
            <option value="">Todos los pagos</option>
            <option value="pending" @selected(request('payment_status') === 'pending')>Pendiente</option>
            <option value="partial" @selected(request('payment_status') === 'partial')>Parcial</option>
            <option value="paid" @selected(request('payment_status') === 'paid')>Pagada</option>
        </select>
        <button type="submit" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Buscar</button>
        @if (request('search') || request('status') || request('payment_status'))
            <a href="{{ route('sales.index') }}" class="text-gray-600 hover:underline self-center">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($sales as $sale)
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
                        $paymentBadge = match ($sale->payment_status) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'partial' => 'bg-blue-100 text-blue-800',
                            'paid' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                        $paymentLabel = match ($sale->payment_status) {
                            'pending' => 'Pendiente',
                            'partial' => 'Parcial',
                            'paid' => 'Pagada',
                            default => $sale->payment_status,
                        };
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $sale->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $sale->client->name }}</td>
                        <td class="px-4 py-3">{{ $sale->sale_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded px-2 py-1 text-xs font-medium {{ $statusBadge }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded px-2 py-1 text-xs font-medium {{ $paymentBadge }}">{{ $paymentLabel }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if (auth()->user()->hasPermission('sales.view'))
                                <a href="{{ route('sales.show', $sale) }}" class="text-gray-600 hover:underline">Ver</a>
                            @endif
                            @if (auth()->user()->hasPermission('sales.delete') && $sale->status === 'draft')
                                <form method="POST" action="{{ route('sales.destroy', $sale) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar esta venta en borrador?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No hay ventas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sales->links() }}</div>
@endsection