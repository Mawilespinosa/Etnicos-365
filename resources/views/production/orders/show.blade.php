@extends('layouts.app')

@section('title', 'Orden ' . $order->code)

@section('content')
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

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold">Orden {{ $order->code }}</h1>
            <span class="inline-block rounded px-2 py-1 text-xs font-medium {{ $badge }}">{{ $label }}</span>
        </div>
        <div class="flex items-center gap-3">
            @if (auth()->user()->hasPermission('production.update') && $order->status === 'pending' && $order->current_stage === 1)
                <a href="{{ route('production.orders.edit', $order) }}"
                   class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                    Editar
                </a>
            @endif
            @if (auth()->user()->hasPermission('production.update') && ! in_array($order->status, ['completed', 'cancelled']))
                <form method="POST" action="{{ route('production.orders.cancel', $order) }}"
                      onsubmit="return confirm('¿Cancelar esta orden de producción?')">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white rounded px-4 py-2 hover:bg-red-700">Cancelar</button>
                </form>
            @endif
            @if (auth()->user()->hasPermission('production.delete'))
                <form method="POST" action="{{ route('production.orders.destroy', $order) }}"
                      onsubmit="return confirm('¿Eliminar esta orden de producción?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                </form>
            @endif
            <a href="{{ route('production.orders.index') }}" class="text-gray-600 hover:underline">Volver</a>
        </div>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Producto</dt>
                <dd class="mt-1">{{ $order->product->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Cantidad</dt>
                <dd class="mt-1">{{ $order->quantity }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Etapa actual</dt>
                <dd class="mt-1">
                    @if ($order->status === 'completed')
                        Completada
                    @elseif ($order->status === 'cancelled')
                        —
                    @else
                        {{ $order->current_stage }} / {{ config('production.total_stages') }}
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Creada por</dt>
                <dd class="mt-1">{{ $order->creator?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Iniciada</dt>
                <dd class="mt-1">{{ $order->started_at ? $order->started_at->format('d/m/Y H:i') : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Completada</dt>
                <dd class="mt-1">{{ $order->completed_at ? $order->completed_at->format('d/m/Y H:i') : '—' }}</dd>
            </div>
        </dl>
        @if ($order->notes)
            <div class="mt-4">
                <dt class="text-xs font-medium text-gray-500 uppercase">Notas</dt>
                <dd class="mt-1">{{ $order->notes }}</dd>
            </div>
        @endif
    </div>

    <div class="bg-white rounded shadow p-6">
        <h2 class="text-lg font-bold mb-4">Etapas de producción</h2>

        <ol class="space-y-3">
            @foreach ($order->stages->sortBy('stage_number') as $stage)
                @php
                    $isCurrent = $stage->stage_number === $order->current_stage
                        && ! in_array($order->status, ['completed', 'cancelled']);
                    $isCompleted = $stage->status === 'completed';
                    $icon = $isCompleted ? '✓' : ($isCurrent ? '●' : '○');
                    $ring = $isCompleted
                        ? 'bg-green-100 text-green-700 border-green-300'
                        : ($isCurrent ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-gray-100 text-gray-400 border-gray-200');
                @endphp
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-sm font-bold {{ $ring }}">
                        {{ $icon }}
                    </span>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-medium {{ $isCompleted || $isCurrent ? 'text-gray-900' : 'text-gray-500' }}">
                                {{ $stage->stage_number }}. {{ $stage->name }}
                            </p>
                            @if ($isCompleted)
                                <span class="text-xs text-green-700">
                                    Completada {{ $stage->completed_at?->format('d/m/Y H:i') }}
                                    @if ($stage->completedBy)
                                        por {{ $stage->completedBy->name }}
                                    @endif
                                </span>
                            @elseif ($isCurrent)
                                <span class="text-xs text-blue-700">Etapa actual</span>
                            @endif
                        </div>
                        @if ($stage->notes)
                            <p class="text-sm text-gray-500 mt-1">{{ $stage->notes }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        @if (auth()->user()->hasPermission('production.advance') && ! in_array($order->status, ['completed', 'cancelled']))
            <form method="POST" action="{{ route('production.orders.advance', $order) }}" class="mt-6"
                  onsubmit="return confirm('¿Completar la etapa actual y avanzar?')">
                @csrf
                <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">
                    Avanzar etapa
                </button>
            </form>
        @endif
    </div>
@endsection