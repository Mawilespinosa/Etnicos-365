@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Proveedores</h1>
        @if (auth()->user()->hasPermission('suppliers.create'))
            <a href="{{ route('suppliers.create') }}"
               class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                Nuevo proveedor
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('suppliers.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" aria-label="Buscar" placeholder="Buscar por nombre, documento, correo o contacto"
               class="w-full max-w-md rounded border border-gray-300 px-3 py-2">
        <button type="submit" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Buscar</button>
        @if (request('search'))
            <a href="{{ route('suppliers.index') }}" class="text-gray-600 hover:underline self-center">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ciudad</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="px-4 py-3">{{ $supplier->name }}</td>
                        <td class="px-4 py-3">{{ $supplier->document_type }} {{ $supplier->document_number }}</td>
                        <td class="px-4 py-3">{{ $supplier->contact_name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $supplier->email ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $supplier->city ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $supplier->is_active ? 'Activo' : 'Inactivo' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if (auth()->user()->hasPermission('suppliers.update'))
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-600 hover:underline">Editar</a>
                            @endif
                            @if (auth()->user()->hasPermission('suppliers.delete'))
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar este proveedor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No hay proveedores registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $suppliers->links() }}</div>
@endsection