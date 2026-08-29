<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Inventory;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Report selector with date filters and export buttons.
     */
    public function index(): View
    {
        return view('reports.index');
    }

    /**
     * Sales report (confirmed sales) as PDF or CSV.
     */
    public function sales(ReportRequest $request): Response|StreamedResponse
    {
        $from = $this->fromDate($request);
        $to = $this->toDate($request);

        $sales = Sale::with(['client', 'seller'])
            ->where('status', 'confirmed')
            ->whereDate('sale_date', '>=', $from)
            ->whereDate('sale_date', '<=', $to)
            ->orderBy('sale_date')
            ->orderBy('id')
            ->get();

        $total = round((float) $sales->sum('total'), 2);

        if ($request->input('format') === 'csv') {
            $headers = ['Factura', 'Fecha', 'Cliente', 'Vendedor', 'Subtotal', 'Descuento', 'IVA', 'Total'];
            $rows = $sales->map(fn (Sale $sale): array => [
                $sale->invoice_number,
                $sale->sale_date->format('d/m/Y'),
                $sale->client?->name ?? '',
                $sale->seller?->name ?? '',
                number_format((float) $sale->subtotal, 2, ',', '.'),
                number_format((float) $sale->discount, 2, ',', '.'),
                number_format((float) $sale->tax, 2, ',', '.'),
                number_format((float) $sale->total, 2, ',', '.'),
            ])->all();
            $rows[] = ['', '', '', 'TOTAL', '', '', '', number_format($total, 2, ',', '.')];

            return $this->exportCsv('reporte-ventas.csv', $headers, $rows);
        }

        return $this->exportPdf('reports.pdf.sales', compact('sales', 'total', 'from', 'to'), 'reporte-ventas.pdf');
    }

    /**
     * Inventory report (finished-goods stock) as PDF or CSV.
     */
    public function inventory(ReportRequest $request): Response|StreamedResponse
    {
        $inventory = Inventory::with('product')
            ->orderBy('product_id')
            ->get();

        $totalValue = round(
            $inventory->sum(fn (Inventory $item): float => (float) $item->stock_qty * (float) ($item->product?->cost ?? 0)),
            2
        );

        if ($request->input('format') === 'csv') {
            $headers = ['Código', 'Producto', 'Ubicación', 'Stock', 'Stock mínimo', 'Costo unitario', 'Valor total'];
            $rows = $inventory->map(fn (Inventory $item): array => [
                $item->product?->code ?? '',
                $item->product?->name ?? '',
                $item->location ?? '',
                $item->stock_qty,
                $item->min_stock,
                number_format((float) ($item->product?->cost ?? 0), 2, ',', '.'),
                number_format((float) $item->stock_qty * (float) ($item->product?->cost ?? 0), 2, ',', '.'),
            ])->all();
            $rows[] = ['', '', '', '', '', 'VALOR TOTAL', number_format($totalValue, 2, ',', '.')];

            return $this->exportCsv('reporte-inventario.csv', $headers, $rows);
        }

        return $this->exportPdf('reports.pdf.inventory', compact('inventory', 'totalValue'), 'reporte-inventario.pdf');
    }

    /**
     * Financial report (incomes, expenses, profit) as PDF or CSV.
     */
    public function financial(ReportRequest $request): Response|StreamedResponse
    {
        $from = $this->fromDate($request);
        $to = $this->toDate($request);

        $incomes = Income::with('user')
            ->whereDate('income_date', '>=', $from)
            ->whereDate('income_date', '<=', $to)
            ->orderBy('income_date')
            ->orderBy('id')
            ->get();

        $expenses = Expense::with('user')
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->orderBy('expense_date')
            ->orderBy('id')
            ->get();

        $totalIncome = round((float) $incomes->sum('amount'), 2);
        $totalExpense = round((float) $expenses->sum('amount'), 2);
        $profit = round($totalIncome - $totalExpense, 2);

        if ($request->input('format') === 'csv') {
            $headers = ['Tipo', 'Fecha', 'Descripción', 'Categoría', 'Monto'];
            $rows = [];

            foreach ($incomes as $income) {
                $rows[] = [
                    'Ingreso',
                    $income->income_date->format('d/m/Y'),
                    $income->description ?? '',
                    $income->type === 'sale' ? 'Venta' : 'Otro',
                    number_format((float) $income->amount, 2, ',', '.'),
                ];
            }

            foreach ($expenses as $expense) {
                $rows[] = [
                    'Egreso',
                    $expense->expense_date->format('d/m/Y'),
                    $expense->description ?? '',
                    $this->expenseCategoryLabel($expense->category),
                    number_format((float) $expense->amount, 2, ',', '.'),
                ];
            }

            $rows[] = ['', '', '', 'TOTAL INGRESOS', number_format($totalIncome, 2, ',', '.')];
            $rows[] = ['', '', '', 'TOTAL EGRESOS', number_format($totalExpense, 2, ',', '.')];
            $rows[] = ['', '', '', 'UTILIDAD', number_format($profit, 2, ',', '.')];

            return $this->exportCsv('reporte-financiero.csv', $headers, $rows);
        }

        return $this->exportPdf(
            'reports.pdf.financial',
            compact('incomes', 'expenses', 'totalIncome', 'totalExpense', 'profit', 'from', 'to'),
            'reporte-financiero.pdf'
        );
    }

    private function exportPdf(string $view, array $data, string $filename): Response
    {
        return Pdf::loadView($view, $data)->download($filename);
    }

    /**
     * Stream a CSV file compatible with Excel (UTF-8 BOM, ";" separator).
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    private function exportCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $stream = fopen('php://output', 'w');

            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headers, ';');

            foreach ($rows as $row) {
                fputcsv($stream, $row, ';');
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function fromDate(ReportRequest $request): string
    {
        return $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'))->startOfDay()->toDateString()
            : now()->startOfMonth()->toDateString();
    }

    private function toDate(ReportRequest $request): string
    {
        return $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'))->endOfDay()->toDateString()
            : now()->endOfMonth()->toDateString();
    }

    private function expenseCategoryLabel(string $category): string
    {
        return match ($category) {
            'raw_material' => 'Materia prima',
            'labor' => 'Mano de obra',
            'services' => 'Servicios',
            default => 'Otro',
        };
    }
}
