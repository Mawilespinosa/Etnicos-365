<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte financiero</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .subtitle { color: #6b7280; margin-bottom: 16px; }
        h2 { font-size: 14px; margin: 20px 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f3f4f6; text-align: left; padding: 6px 8px; border-bottom: 1px solid #d1d5db; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; border-top: 2px solid #374151; }
        .summary { margin-top: 20px; }
        .summary td { border: none; padding: 4px 8px; }
    </style>
</head>
<body>
    <h1>Reporte financiero</h1>
    <p class="subtitle">Período: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</p>

    <h2>Ingresos</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incomes as $income)
                <tr>
                    <td>{{ $income->income_date->format('d/m/Y') }}</td>
                    <td>{{ $income->description ?? '—' }}</td>
                    <td>{{ $income->type === 'sale' ? 'Venta' : 'Otro' }}</td>
                    <td class="right">${{ number_format($income->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay ingresos en el período seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Egresos</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td>{{ $expense->description ?? '—' }}</td>
                    <td>{{ match ($expense->category) {
                        'raw_material' => 'Materia prima',
                        'labor' => 'Mano de obra',
                        'services' => 'Servicios',
                        default => 'Otro',
                    } }}</td>
                    <td class="right">${{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay egresos en el período seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Total ingresos</td>
            <td class="right">${{ number_format($totalIncome, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total egresos</td>
            <td class="right">${{ number_format($totalExpense, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Utilidad</td>
            <td class="right">${{ number_format($profit, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>