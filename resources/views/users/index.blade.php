@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Usuarios</h1>
        @if (auth()->user()->hasPermission('users.create'))
            <a href="{{ route('users.create') }}"
               class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                Nuevo usuario
            </a>
        @endif
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roles</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @forelse ($user->roles as $role)
                                <span class="inline-block bg-gray-200 rounded px-2 py-0.5 text-xs mr-1">
                                    {{ $role->display_name }}
                                </span>
                            @empty
                                <span class="text-gray-400 text-sm">Sin rol</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if (auth()->user()->hasPermission('users.update'))
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Editar</a>
                            @endif
                            @if (auth()->user()->hasPermission('users.delete') && auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay usuarios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection