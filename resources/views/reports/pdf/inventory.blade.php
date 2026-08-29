<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de inventario</title>
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
    <h1>Reporte de inventario</h1>
    <p class="subtitle">Generado el {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Ubicación</th>
                <th class="right">Stock</th>
                <th class="right">Stock mínimo</th>
                <th class="right">Costo unitario</th>
                <th class="right">Valor total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inventory as $item)
                <tr>
                    <td>{{ $item->product?->code ?? '—' }}</td>
                    <td>{{ $item->product?->name ?? '—' }}</td>
                    <td>{{ $item->location ?? '—' }}</td>
                    <td class="right">{{ $item->stock_qty }}</td>
                    <td class="right">{{ $item->min_stock }}</td>
                    <td class="right">${{ number_format($item->product?->cost ?? 0, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format(($item->stock_qty ?? 0) * ($item->product?->cost ?? 0), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay productos registrados en el inventario.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6">VALOR TOTAL</td>
                <td class="right">${{ number_format($totalValue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>