@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Nuevo producto</h1>

    <form method="POST" action="{{ route('products.store') }}" class="bg-white rounded shadow p-6 max-w-2xl" enctype="multipart/form-data">
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

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium mb-1">Descripción</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full rounded border border-gray-300 px-3 py-2">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="size" class="block text-sm font-medium mb-1">Talla</label>
                <input id="size" type="text" name="size" value="{{ old('size') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('size')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="color" class="block text-sm font-medium mb-1">Color</label>
                <input id="color" type="text" name="color" value="{{ old('color') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('color')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="model" class="block text-sm font-medium mb-1">Modelo</label>
                <input id="model" type="text" name="model" value="{{ old('model') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('model')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium mb-1">Categoría</label>
                <input id="category" type="text" name="category" value="{{ old('category') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('category')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="cost" class="block text-sm font-medium mb-1">Costo</label>
                <input id="cost" type="number" step="0.01" min="0" name="cost" value="{{ old('cost') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('cost')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium mb-1">Precio de venta</label>
                <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="stock_qty" class="block text-sm font-medium mb-1">Stock inicial</label>
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
        </div>

        <div class="mb-4">
            <label for="image" class="block text-sm font-medium mb-1">Imagen del producto</label>
            <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('image')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, WebP. Máximo 2 MB.</p>
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded">
                <span class="text-sm font-medium">Producto activo</span>
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Guardar</button>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
@endsection