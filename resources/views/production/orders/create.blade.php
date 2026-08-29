@extends('layouts.app')

@section('title', 'Nueva orden de producción')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Nueva orden de producción</h1>

    <form method="POST" action="{{ route('production.orders.store') }}" class="bg-white rounded shadow p-6 max-w-2xl">
        @csrf

        <div class="mb-4">
            <label for="product_id" class="block text-sm font-medium mb-1">Producto</label>
            <select id="product_id" name="product_id" required
                    class="w-full rounded border border-gray-300 px-3 py-2">
                <option value="">— Seleccionar producto —</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                        {{ $product->code }} — {{ $product->name }}
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="quantity" class="block text-sm font-medium mb-1">Cantidad</label>
            <input id="quantity" type="number" step="0.01" min="0.01" name="quantity" value="{{ old('quantity') }}" required
                   class="w-full rounded border border-gray-300 px-3 py-2">
            @error('quantity')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium mb-1">Notas</label>
            <textarea id="notes" name="notes" rows="3"
                      class="w-full rounded border border-gray-300 px-3 py-2">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Guardar</button>
            <a href="{{ route('production.orders.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
@endsection