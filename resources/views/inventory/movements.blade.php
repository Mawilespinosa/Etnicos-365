@extends('layouts.app')

@section('title', 'Movimientos de inventario')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Movimientos de inventario</h1>
        <a href="{{ route('inventory.index') }}" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">
            Volver al inventario
        </a>
    </div>

    @if (auth()->user()->hasPermission('inventory.movements'))
        <div class="bg-white rounded shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Registrar movimiento manual</h2>
            <form method="POST" action="{{ route('inventory.movements.store') }}"
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @csrf
                <div>
                    <label for="product_id" class="block text-sm font-medium mb-1">Producto</label>
                    <select id="product_id" name="product_id" required
                            class="w-full rounded border border-gray-300 px-3 py-2">
                        <option value="">— Seleccionar —</option>
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
                <div>
                    <label for="type" class="block text-sm font-medium mb-1">Tipo</label>
                    <select id="type" name="type" required
                            class="w-full rounded border border-gray-300 px-3 py-2">
                        <option value="in" @selected(old('type') === 'in')>Entrada (in)</option>
                        <option value="out" @selected(old('type') === 'out')>Salida (out)</option>
                        <option value="adjustment" @selected(old('type') === 'adjustment')>Ajuste</option>
                    </select>
                    @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium mb-1">Cantidad</label>
                    <input id="quantity" type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" required
                           class="w-full rounded border border-gray-300 px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">En ajustes use signo negativo para restar.</p>
                    @error('quantity')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="lg:col-span-2">
                    <label for="reason" class="block text-sm font-medium mb-1">Motivo</label>
                    <input id="reason" type="text" name="reason" value="{{ old('reason') }}" required
                           class="w-full rounded border border-gray-300 px-3 py-2"
                           placeholder="Ej: conteo físico, merma, devolución">
                    @error('reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="lg:col-span-5">
                    <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                        Registrar movimiento
                    </button>
                </div>
            </form>
        </div>
    @endif

    <form method="GET" action="{{ route('inventory.movements') }}" class="mb-4 flex flex-wrap gap-2">
        <select name="product_id" aria-label="Filtrar por producto" class="rounded border border-gray-300 px-3 py-2">
            <option value="">Todos los productos</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                    {{ $product->code }} — {{ $product->name }}
                </option>
            @endforeach
        </select>
        <select name="type" aria-label="Filtrar por tipo" class="rounded border border-gray-300 px-3 py-2">
            <option value="">Todos los tipos</option>
            <option value="in" @selected(request('type') === 'in')>Entrada</option>
            <option value="out" @selected(request('type') === 'out')>Salida</option>
            <option value="adjustment" @selected(request('type') === 'adjustment')>Ajuste</option>
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="rounded border border-gray-300 px-3 py-2">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="rounded border border-gray-300 px-3 py-2">
        <button type="submit" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Filtrar</button>
        @if (request('product_id') || request('type') || request('date_from') || request('date_to'))
            <a href="{{ route('inventory.movements') }}" class="text-gray-600 hover:underline self-center">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($movements as $movement)
                    <tr>
                        <td class="px-4 py-3">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $movement->product->name }}</span>
                            <span class="text-gray-500 text-sm block">{{ $movement->product->code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match ($movement->type) {
                                    'in' => 'bg-green-100 text-green-800',
                                    'out' => 'bg-red-100 text-red-800',
                                    'adjustment' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $label = match ($movement->type) {
                                    'in' => 'Entrada',
                                    'out' => 'Salida',
                                    'adjustment' => 'Ajuste',
                                    default => $movement->type,
                                };
                            @endphp
                            <span class="inline-block rounded px-2 py-1 text-xs font-medium {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($movement->type === 'in')
                                <span class="text-green-700">+{{ $movement->quantity }}</span>
                            @elseif ($movement->type === 'out')
                                <span class="text-red-700">-{{ $movement->quantity }}</span>
                            @else
                                <span class="text-yellow-700">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $movement->reason ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $movement->user?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $movements->links() }}</div>
@endsection