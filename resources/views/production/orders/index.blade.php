@extends('layouts.app')

@section('title', 'Órdenes de producción')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Órdenes de producción</h1>
        @if (auth()->user()->hasPermission('production.create'))
            <a href="{{ route('production.orders.create') }}"
               class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                Nueva orden
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('production.orders.index') }}" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" aria-label="Buscar" placeholder="Buscar por código o producto"
               class="w-full max-w-md rounded border border-gray-300 px-3 py-2">
        <select name="status" aria-label="Filtrar por estado" class="rounded border border-gray-300 px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="pending" @selected(request('status') === 'pending')>Pendiente</option>
            <option value="in_progress" @selected(request('status') === 'in_progress')>En proceso</option>
            <option value="completed" @selected(request('status') === 'completed')>Completada</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelada</option>
        </select>
        <button type="submit" class="bg-white border border-gray-300 rounded px-4 py-2 hover:bg-gray-50">Buscar</button>
        @if (request('search') || request('status'))
            <a href="{{ route('production.orders.index') }}" class="text-gray-600 hover:underline self-center">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Etapa actual</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $order->code }}</td>
                        <td class="px-4 py-3">{{ $order->product->name }}</td>
                        <td class="px-4 py-3">{{ $order->quantity }}</td>
                        <td class="px-4 py-3">
                            @if ($order->status === 'completed')
                                Completada
                            @elseif ($order->status === 'cancelled')
                                —
                            @else
                                {{ $order->current_stage }} / {{ config('production.total_stages') }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match ($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $label = match ($order->status) {
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En proceso',
                                    'completed' => 'Completada',
                                    'cancelled' => 'Cancelada',
                                    default => $order->status,
                                };
                            @endphp
                            <span class="inline-block rounded px-2 py-1 text-xs font-medium {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if (auth()->user()->hasPermission('production.view'))
                                <a href="{{ route('production.orders.show', $order) }}" class="text-gray-600 hover:underline">Ver</a>
                            @endif
                            @if (auth()->user()->hasPermission('production.update') && $order->status === 'pending' && $order->current_stage === 1)
                                <a href="{{ route('production.orders.edit', $order) }}" class="text-blue-600 hover:underline ml-2">Editar</a>
                            @endif
                            @if (auth()->user()->hasPermission('production.delete'))
                                <form method="POST" action="{{ route('production.orders.destroy', $order) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar esta orden de producción?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay órdenes de producción registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection