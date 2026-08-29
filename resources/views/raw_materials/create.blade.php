@extends('layouts.app')

@section('title', 'Nueva materia prima')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Nueva materia prima</h1>

    <form method="POST" action="{{ route('raw-materials.store') }}" class="bg-white rounded shadow p-6 max-w-2xl">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium mb-1">Código</label>
                <input id="code" type="text" name="code" value="{{ old('code') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-1">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="category" class="block text-sm font-medium mb-1">Categoría</label>
                <input id="category" type="text" name="category" value="{{ old('category') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('category')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="unit" class="block text-sm font-medium mb-1">Unidad de medida</label>
                <select id="unit" name="unit" required class="w-full rounded border border-gray-300 px-3 py-2">
                    <option value="unit" @selected(old('unit', 'unit') === 'unit')>Unidad</option>
                    <option value="meter" @selected(old('unit') === 'meter')>Metro</option>
                    <option value="kg" @selected(old('unit') === 'kg')>Kilogramo</option>
                    <option value="roll" @selected(old('unit') === 'roll')>Rollo</option>
                </select>
                @error('unit')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="mb-4">
                <label for="stock_qty" class="block text-sm font-medium mb-1">Stock</label>
                <input id="stock_qty" type="number" step="0.01" min="0" name="stock_qty" value="{{ old('stock_qty', 0) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('stock_qty')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="min_stock" class="block text-sm font-medium mb-1">Stock mínimo</label>
                <input id="min_stock" type="number" step="0.01" min="0" name="min_stock" value="{{ old('min_stock', 0) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('min_stock')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="cost" class="block text-sm font-medium mb-1">Costo</label>
                <input id="cost" type="number" step="0.01" min="0" name="cost" value="{{ old('cost', 0) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('cost')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded">
                <span class="text-sm font-medium">Materia prima activa</span>
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Guardar</button>
            <a href="{{ route('raw-materials.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
@endsection