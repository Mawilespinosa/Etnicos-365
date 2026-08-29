@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Productos</h1>
        @if (auth()->user()->hasPermission('products.create'))
            <a href="{{ route('products.create') }}"
               class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                Nuevo producto
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" aria-label="Buscar" placeholder="Buscar por nombre, código, modelo o categoría"
               class="w-full max-w-md rounded border border-gray-300 px-3 py-2">
        <button type="submit" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Buscar</button>
        @if (request('search'))
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline self-center">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Talla</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-4 py-3">{{ $product->code }}</td>
                        <td class="px-4 py-3">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->size ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $product->color ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $product->category ?? '—' }}</td>
                        <td class="px-4 py-3">${{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $product->stock_qty }}</td>
                        <td class="px-4 py-3">{{ $product->is_active ? 'Activo' : 'Inactivo' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if (auth()->user()->hasPermission('products.view'))
                                <a href="{{ route('products.show', $product) }}" class="text-gray-600 hover:underline">Ver</a>
                            @endif
                            @if (auth()->user()->hasPermission('products.update'))
                                <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:underline ml-2">Editar</a>
                            @endif
                            @if (auth()->user()->hasPermission('products.delete'))
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection