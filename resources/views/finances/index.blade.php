@extends('layouts.app')

@section('title', 'Finanzas')

@section('content')
    @php
        $incomeTypeLabel = fn (string $type): string => $type === 'sale' ? 'Venta' : 'Otro';
        $expenseCategoryLabel = fn (string $category): string => match ($category) {
            'raw_material' => 'Materia prima',
            'labor' => 'Mano de obra',
            'services' => 'Servicios',
            default => 'Otro',
        };
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold">Finanzas</h1>
        <form method="GET" action="{{ route('finances.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Desde</label>
                <input type="date" id="date_from" name="date_from" value="{{ $from }}"
                       class="mt-1 rounded border-gray-300 border px-3 py-2">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Hasta</label>
                <input type="date" id="date_to" name="date_to" value="{{ $to }}"
                       class="mt-1 rounded border-gray-300 border px-3 py-2">
            </div>
            <button type="submit" class="bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">Filtrar</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded shadow p-5">
            <p class="text-sm text-gray-500">Total ingresos</p>
            <p class="text-2xl font-bold text-green-700">${{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded shadow p-5">
            <p class="text-sm text-gray-500">Total egresos</p>
            <p class="text-2xl font-bold text-red-700">${{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded shadow p-5">
            <p class="text-sm text-gray-500">Utilidad</p>
            <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-gray-900' : 'text-red-700' }}">
                ${{ number_format($profit, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-4">Registrar ingreso</h2>
            <form method="POST" action="{{ route('finances.incomes.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="income_description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input type="text" id="income_description" name="description" value="{{ old('description') }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="income_amount" class="block text-sm font-medium text-gray-700">Monto</label>
                        <input type="number" id="income_amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}"
                               class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                        @error('amount')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="income_date" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" id="income_date" name="income_date" value="{{ old('income_date', now()->toDateString()) }}"
                               class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                        @error('income_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="bg-green-700 text-white rounded px-4 py-2 hover:bg-green-600">
                    Registrar ingreso
                </button>
            </form>
        </div>

        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-4">Registrar egreso</h2>
            <form method="POST" action="{{ route('finances.expenses.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="expense_category" class="block text-sm font-medium text-gray-700">Categoría</label>
                    <select id="expense_category" name="category" class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                        <option value="raw_material" @selected(old('category') === 'raw_material')>Materia prima</option>
                        <option value="labor" @selected(old('category') === 'labor')>Mano de obra</option>
                        <option value="services" @selected(old('category') === 'services')>Servicios</option>
                        <option value="other" @selected(old('category') === 'other')>Otro</option>
                    </select>
                    @error('category')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="expense_description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input type="text" id="expense_description" name="description" value="{{ old('description') }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="expense_amount" class="block text-sm font-medium text-gray-700">Monto</label>
                        <input type="number" id="expense_amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}"
                               class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                        @error('amount')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}"
                               class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                        @error('expense_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="bg-red-700 text-white rounded px-4 py-2 hover:bg-red-600">
                    Registrar egreso
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow overflow-x-auto">
            <div class="px-5 py-4 border-b">
                <h2 class="text-lg font-bold">Ingresos</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($incomes as $income)
                        <tr>
                            <td class="px-4 py-3">{{ $income->income_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $income->description }}</td>
                            <td class="px-4 py-3">{{ $incomeTypeLabel($income->type) }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($income->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if (auth()->user()->hasPermission('finances.delete') && $income->type === 'other' && $income->reference_type === null)
                                    <form method="POST" action="{{ route('finances.incomes.destroy', $income) }}"
                                          onsubmit="return confirm('¿Eliminar este ingreso?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay ingresos en el rango seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $incomes->links() }}</div>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <div class="px-5 py-4 border-b">
                <h2 class="text-lg font-bold">Egresos</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($expenses as $expense)
                        <tr>
                            <td class="px-4 py-3">{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $expense->description }}</td>
                            <td class="px-4 py-3">{{ $expenseCategoryLabel($expense->category) }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if (auth()->user()->hasPermission('finances.delete'))
                                    <form method="POST" action="{{ route('finances.expenses.destroy', $expense) }}"
                                          onsubmit="return confirm('¿Eliminar este egreso?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay egresos en el rango seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $expenses->links() }}</div>
        </div>
    </div>
@endsection