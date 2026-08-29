@extends('layouts.app')

@section('title', 'Editar proveedor')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Editar proveedor</h1>

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="bg-white rounded shadow p-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-1">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="document_type" class="block text-sm font-medium mb-1">Tipo de documento</label>
                <select id="document_type" name="document_type" required class="w-full rounded border border-gray-300 px-3 py-2">
                    <option value="NIT" @selected(old('document_type', $supplier->document_type) === 'NIT')>NIT</option>
                    <option value="CC" @selected(old('document_type', $supplier->document_type) === 'CC')>Cédula de ciudadanía (CC)</option>
                    <option value="CE" @selected(old('document_type', $supplier->document_type) === 'CE')>Cédula de extranjería (CE)</option>
                </select>
                @error('document_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="document_number" class="block text-sm font-medium mb-1">Número de documento</label>
                <input id="document_number" type="text" name="document_number" value="{{ old('document_number', $supplier->document_number) }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('document_number')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="contact_name" class="block text-sm font-medium mb-1">Persona de contacto</label>
                <input id="contact_name" type="text" name="contact_name" value="{{ old('contact_name', $supplier->contact_name) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('contact_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium mb-1">Teléfono</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium mb-1">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email', $supplier->email) }}"
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium mb-1">Dirección</label>
                <input id="address" type="text" name="address" value="{{ old('address', $supplier->address) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="city" class="block text-sm font-medium mb-1">Ciudad</label>
                <input id="city" type="text" name="city" value="{{ old('city', $supplier->city) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('city')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked($supplier->is_active) class="rounded">
                <span class="text-sm font-medium">Proveedor activo</span>
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Guardar</button>
            <a href="{{ route('suppliers.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
@endsection