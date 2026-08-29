@extends('layouts.app')

@section('title', 'Roles')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Roles</h1>
        @if (auth()->user()->hasPermission('roles.create'))
            <a href="{{ route('roles.create') }}"
               class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                Nuevo rol
            </a>
        @endif
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permisos</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($roles as $role)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $role->display_name }}</td>
                        <td class="px-4 py-3">{{ $role->description }}</td>
                        <td class="px-4 py-3">{{ $role->permissions_count }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if (auth()->user()->hasPermission('roles.update'))
                                <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:underline">Editar</a>
                            @endif
                            @if (auth()->user()->hasPermission('roles.delete') && $role->name !== 'admin')
                                <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar este rol?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay roles registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $roles->links() }}</div>
@endsection