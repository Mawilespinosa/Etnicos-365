@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Nuevo usuario</h1>

    <form method="POST" action="{{ route('users.store') }}" class="bg-white rounded shadow p-6 max-w-2xl">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-1">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium mb-1">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium mb-1">Contraseña</label>
            <input id="password" type="password" name="password" required
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="w-full rounded border border-gray-300 px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded">
                <span class="text-sm font-medium">Usuario activo</span>
            </label>
        </div>

        <div class="mb-6">
            <span class="block text-sm font-medium mb-2">Roles</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                               @checked(in_array($role->id, old('roles', []))) class="rounded">
                        <span>{{ $role->display_name }}</span>
                    </label>
                @endforeach
            </div>
            @error('roles')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Guardar</button>
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
@endsection