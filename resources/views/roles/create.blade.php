@extends('layouts.app')

@section('title', 'Nuevo rol')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Nuevo rol</h1>

    <form method="POST" action="{{ route('roles.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-1">Nombre (identificador)</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                   placeholder="ej: cajero"
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="display_name" class="block text-sm font-medium mb-1">Nombre para mostrar</label>
            <input id="display_name" type="text" name="display_name" value="{{ old('display_name') }}" required
                   placeholder="ej: Cajero"
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('display_name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium mb-1">Descripción</label>
            <input id="description" type="text" name="description" value="{{ old('description') }}"
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <span class="block text-sm font-medium mb-2">Permisos</span>
            @foreach ($permissions as $module => $modulePermissions)
                <fieldset class="mb-4 border border-gray-200 rounded p-4">
                    <legend class="text-sm font-semibold px-2">{{ $moduleLabels[$module] ?? ucfirst($module) }}</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($modulePermissions as $permission)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       @checked(in_array($permission->id, old('permissions', []))) class="rounded">
                                <span>{{ $permission->display_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
            @error('permissions')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Guardar</button>
            <a href="{{ route('roles.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
@endsection