<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .subtitle { color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f3f4f6; text-align: left; padding: 6px 8px; border-bottom: 1px solid #d1d5db; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; border-top: 2px solid #374151; }
    </style>
</head>
<body>
    <h1>Reporte de ventas</h1>
    <p class="subtitle">Período: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="right">Subtotal</th>
                <th class="right">Descuento</th>
                <th class="right">IVA</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                    <td>{{ $sale->client?->name ?? '—' }}</td>
                    <td>{{ $sale->seller?->name ?? '—' }}</td>
                    <td class="right">${{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($sale->discount, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($sale->tax, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($sale->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay ventas confirmadas en el período seleccionado.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="7">TOTAL</td>
                <td class="right">${{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>