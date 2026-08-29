@extends('layouts.app')

@section('title', 'Alertas de stock')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Alertas de stock</h1>
        <a href="{{ route('inventory.index') }}" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">
            Volver al inventario
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock mínimo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($alerts as $product)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $product->name }}</span>
                            <span class="text-gray-500 text-sm block">{{ $product->code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded bg-red-100 text-red-800 px-2 py-1 text-xs font-medium">
                                {{ $product->stock_qty }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $product->min_stock }}</td>
                        <td class="px-4 py-3">{{ $product->inventory?->location ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            No hay productos con stock bajo el mínimo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $alerts->links() }}</div>
@endsection