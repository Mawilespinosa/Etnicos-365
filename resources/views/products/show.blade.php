@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
        <div class="flex items-center gap-3">
            @if (auth()->user()->hasPermission('products.update'))
                <a href="{{ route('products.edit', $product) }}"
                   class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                    Editar producto
                </a>
            @endif
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline">Volver</a>
        </div>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if ($product->image)
                <div class="lg:col-span-1">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Imagen</dt>
                    <dd class="mt-1">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="max-h-48 rounded border border-gray-200">
                    </dd>
                </div>
            @endif
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Código</dt>
                <dd class="mt-1">{{ $product->code }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Categoría</dt>
                <dd class="mt-1">{{ $product->category ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Modelo</dt>
                <dd class="mt-1">{{ $product->model ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Talla</dt>
                <dd class="mt-1">{{ $product->size ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Color</dt>
                <dd class="mt-1">{{ $product->color ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Estado</dt>
                <dd class="mt-1">{{ $product->is_active ? 'Activo' : 'Inactivo' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Costo</dt>
                <dd class="mt-1">${{ number_format($product->cost, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Precio de venta</dt>
                <dd class="mt-1">${{ number_format($product->price, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Stock</dt>
                <dd class="mt-1">{{ $product->stock_qty }} (mínimo {{ $product->min_stock }})</dd>
            </div>
        </div>
        @if ($product->description)
            <div class="mt-4">
                <dt class="text-xs font-medium text-gray-500 uppercase">Descripción</dt>
                <dd class="mt-1">{{ $product->description }}</dd>
            </div>
        @endif
    </div>

    <div class="bg-white rounded shadow p-6">
        <h2 class="text-lg font-bold mb-4">Lista de materiales (BOM)</h2>

        <div class="overflow-x-auto mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia prima</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($product->rawMaterials as $material)
                        <tr>
                            <td class="px-4 py-3">{{ $material->code }}</td>
                            <td class="px-4 py-3">{{ $material->name }}</td>
                            <td class="px-4 py-3">{{ $material->pivot->quantity }}</td>
                            <td class="px-4 py-3">{{ $material->pivot->unit ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $material->pivot->notes ?? '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (auth()->user()->hasPermission('bill_of_materials.delete'))
                                    <form method="POST" action="{{ route('products.materials.destroy', [$product, $material]) }}" class="inline"
                                          onsubmit="return confirm('¿Quitar esta materia prima de la lista?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Quitar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Este producto aún no tiene materias primas asignadas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (auth()->user()->hasPermission('bill_of_materials.create'))
            <h3 class="text-md font-semibold mb-3">Agregar materia prima</h3>
            <form method="POST" action="{{ route('products.materials.store', $product) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @csrf

                <div>
                    <label for="raw_material_id" class="block text-sm font-medium mb-1">Materia prima</label>
                    <select id="raw_material_id" name="raw_material_id" required
                            class="w-full rounded border border-gray-300 px-3 py-2">
                        <option value="">— Seleccionar —</option>
                        @foreach ($availableMaterials as $material)
                            <option value="{{ $material->id }}" @selected(old('raw_material_id') == $material->id)>
                                {{ $material->code }} — {{ $material->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('raw_material_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium mb-1">Cantidad</label>
                    <input id="quantity" type="number" step="0.01" min="0.01" name="quantity" value="{{ old('quantity', 1) }}" required
                           class="w-full rounded border border-gray-300 px-3 py-2">
                    @error('quantity')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium mb-1">Unidad</label>
                    <select id="unit" name="unit" class="w-full rounded border border-gray-300 px-3 py-2">
                        <option value="">— Sin unidad —</option>
                        <option value="unit" @selected(old('unit') === 'unit')>Unidad</option>
                        <option value="meter" @selected(old('unit') === 'meter')>Metro</option>
                        <option value="kg" @selected(old('unit') === 'kg')>Kilogramo</option>
                        <option value="roll" @selected(old('unit') === 'roll')>Rollo</option>
                    </select>
                    @error('unit')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium mb-1">Notas</label>
                    <input id="notes" type="text" name="notes" value="{{ old('notes') }}"
                           class="w-full rounded border border-gray-300 px-3 py-2">
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                        Agregar materia prima
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection