@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Inventario</h1>
        <div class="flex gap-2">
            @if (auth()->user()->hasPermission('inventory.movements'))
                <a href="{{ route('inventory.movements') }}"
                   class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">
                    Movimientos
                </a>
            @endif
            @if (auth()->user()->hasPermission('inventory.view'))
                <a href="{{ route('inventory.alerts') }}"
                   class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">
                    Alertas
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('inventory.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" aria-label="Buscar" placeholder="Buscar por producto o código"
               class="w-full max-w-md rounded border border-gray-300 px-3 py-2">
        <button type="submit" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Buscar</button>
        @if (request('search'))
            <a href="{{ route('inventory.index') }}" class="text-gray-600 hover:underline self-center">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock mínimo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($inventory as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $item->product->name }}</span>
                            <span class="text-gray-500 text-sm block">{{ $item->product->code }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $item->location ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $item->stock_qty }}</td>
                        <td class="px-4 py-3">{{ $item->min_stock }}</td>
                        <td class="px-4 py-3">
                            @if ($item->stock_qty <= $item->min_stock)
                                <span class="inline-block rounded bg-red-100 text-red-800 px-2 py-1 text-xs font-medium">Stock bajo</span>
                            @else
                                <span class="inline-block rounded bg-green-100 text-green-800 px-2 py-1 text-xs font-medium">Disponible</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay productos en inventario.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $inventory->links() }}</div>
@endsection