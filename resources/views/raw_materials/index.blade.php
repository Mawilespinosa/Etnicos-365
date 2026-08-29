@extends('layouts.app')

@section('title', 'Materias primas')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Materias primas</h1>
        @if (auth()->user()->hasPermission('raw_materials.create'))
            <a href="{{ route('raw-materials.create') }}"
               class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                Nueva materia prima
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('raw-materials.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" aria-label="Buscar" placeholder="Buscar por nombre, código o categoría"
               class="w-full max-w-md rounded border border-gray-300 px-3 py-2">
        <button type="submit" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Buscar</button>
        @if (request('search'))
            <a href="{{ route('raw-materials.index') }}" class="text-gray-600 hover:underline self-center">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Costo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($rawMaterials as $material)
                    <tr>
                        <td class="px-4 py-3">{{ $material->code }}</td>
                        <td class="px-4 py-3">{{ $material->name }}</td>
                        <td class="px-4 py-3">{{ $material->category ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $material->unit }}</td>
                        <td class="px-4 py-3">{{ $material->stock_qty }}</td>
                        <td class="px-4 py-3">${{ number_format($material->cost, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $material->is_active ? 'Activo' : 'Inactivo' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if (auth()->user()->hasPermission('raw_materials.update'))
                                <a href="{{ route('raw-materials.edit', $material) }}" class="text-blue-600 hover:underline">Editar</a>
                            @endif
                            @if (auth()->user()->hasPermission('raw_materials.delete'))
                                <form method="POST" action="{{ route('raw-materials.destroy', $material) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar esta materia prima?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay materias primas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rawMaterials->links() }}</div>
@endsection