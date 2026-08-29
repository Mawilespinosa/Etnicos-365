@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Reportes</h1>
        <p class="text-gray-600">Seleccione un reporte, ajuste el rango de fechas y elija el formato de exportación.</p>
    </div>

    @php
        $defaultFrom = now()->startOfMonth()->toDateString();
        $defaultTo = now()->endOfMonth()->toDateString();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-1">Ventas</h2>
            <p class="text-sm text-gray-600 mb-4">Ventas confirmadas con cliente, vendedor y totales.</p>
            <form method="GET" action="{{ route('reports.sales') }}" class="space-y-4">
                <div>
                    <label for="sales_from" class="block text-sm font-medium text-gray-700">Desde</label>
                    <input type="date" id="sales_from" name="date_from" value="{{ $defaultFrom }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                </div>
                <div>
                    <label for="sales_to" class="block text-sm font-medium text-gray-700">Hasta</label>
                    <input type="date" id="sales_to" name="date_to" value="{{ $defaultTo }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="format" value="pdf"
                            class="flex-1 bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">PDF</button>
                    <button type="submit" name="format" value="csv"
                            class="flex-1 bg-green-700 text-white rounded px-4 py-2 hover:bg-green-600">CSV</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-1">Inventario</h2>
            <p class="text-sm text-gray-600 mb-4">Stock de producto terminado con valorización a costo.</p>
            <form method="GET" action="{{ route('reports.inventory') }}" class="space-y-4">
                <div>
                    <label for="inventory_from" class="block text-sm font-medium text-gray-700">Desde</label>
                    <input type="date" id="inventory_from" name="date_from" value="{{ $defaultFrom }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                </div>
                <div>
                    <label for="inventory_to" class="block text-sm font-medium text-gray-700">Hasta</label>
                    <input type="date" id="inventory_to" name="date_to" value="{{ $defaultTo }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="format" value="pdf"
                            class="flex-1 bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">PDF</button>
                    <button type="submit" name="format" value="csv"
                            class="flex-1 bg-green-700 text-white rounded px-4 py-2 hover:bg-green-600">CSV</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-1">Financiero</h2>
            <p class="text-sm text-gray-600 mb-4">Ingresos, egresos y utilidad del período.</p>
            <form method="GET" action="{{ route('reports.financial') }}" class="space-y-4">
                <div>
                    <label for="financial_from" class="block text-sm font-medium text-gray-700">Desde</label>
                    <input type="date" id="financial_from" name="date_from" value="{{ $defaultFrom }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                </div>
                <div>
                    <label for="financial_to" class="block text-sm font-medium text-gray-700">Hasta</label>
                    <input type="date" id="financial_to" name="date_to" value="{{ $defaultTo }}"
                           class="mt-1 w-full rounded border-gray-300 border px-3 py-2">
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="format" value="pdf"
                            class="flex-1 bg-brand-700 text-white rounded px-4 py-2 hover:bg-brand-800">PDF</button>
                    <button type="submit" name="format" value="csv"
                            class="flex-1 bg-green-700 text-white rounded px-4 py-2 hover:bg-green-600">CSV</button>
                </div>
            </form>
        </div>
    </div>
@endsection