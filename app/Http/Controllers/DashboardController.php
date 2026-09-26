<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filterMonth = (int) $request->input('month', now()->month);
        $filterYear = (int) $request->input('year', now()->year);

        $filterDate = now()->year($filterYear)->month($filterMonth);
        $monthStart = $filterDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $filterDate->copy()->endOfMonth()->toDateString();
        $today = now()->toDateString();

        $salesToday = round((float) Sale::where('status', 'confirmed')
            ->whereDate('sale_date', $today)
            ->sum('total'), 2);

        $salesMonth = round((float) Sale::where('status', 'confirmed')
            ->whereDate('sale_date', '>=', $monthStart)
            ->whereDate('sale_date', '<=', $monthEnd)
            ->sum('total'), 2);

        $activeOrders = ProductionOrder::whereIn('status', ['pending', 'in_progress'])->count();

        $lowStock = Product::where('is_active', true)
            ->where(function ($query): void {
                $query->whereColumn('stock_qty', '<=', 'min_stock')
                    ->orWhereHas('inventory', function ($q): void {
                        $q->whereColumn('stock_qty', '<=', 'min_stock');
                    });
            })
            ->count();

        $monthIncome = round((float) Income::whereDate('income_date', '>=', $monthStart)
            ->whereDate('income_date', '<=', $monthEnd)
            ->sum('amount'), 2);
        $monthExpense = round((float) Expense::whereDate('expense_date', '>=', $monthStart)
            ->whereDate('expense_date', '<=', $monthEnd)
            ->sum('amount'), 2);
        $monthProfit = round($monthIncome - $monthExpense, 2);

        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        $years = range(now()->year, now()->year - 5);

        $chartData = $this->getChartData($filterYear);

        return view('dashboard.index', compact(
            'salesToday',
            'salesMonth',
            'activeOrders',
            'lowStock',
            'monthIncome',
            'monthExpense',
            'monthProfit',
            'filterMonth',
            'filterYear',
            'months',
            'years',
            'chartData',
        ));
    }

    private function getChartData(int $year): array
    {
        $monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $salesRaw = Sale::where('status', 'confirmed')
            ->whereYear('sale_date', $year)
            ->get()
            ->mapWithKeys(fn ($s) => [(int) $s->sale_date->format('m') => $s])
            ->groupBy(fn ($s, $k) => $k)
            ->map(fn ($group) => $group->sum('total'));

        $incomeRaw = Income::whereYear('income_date', $year)
            ->get()
            ->mapWithKeys(fn ($i) => [(int) $i->income_date->format('m') => $i])
            ->groupBy(fn ($i, $k) => $k)
            ->map(fn ($group) => $group->sum('amount'));

        $expenseRaw = Expense::whereYear('expense_date', $year)
            ->get()
            ->mapWithKeys(fn ($e) => [(int) $e->expense_date->format('m') => $e])
            ->groupBy(fn ($e, $k) => $k)
            ->map(fn ($group) => $group->sum('amount'));

        $sales = [];
        $incomes = [];
        $expenses = [];
        $profits = [];

        for ($m = 1; $m <= 12; $m++) {
            $s = (int) round($salesRaw->get($m, 0));
            $i = (int) round($incomeRaw->get($m, 0));
            $e = (int) round($expenseRaw->get($m, 0));

            $sales[] = $s;
            $incomes[] = $i;
            $expenses[] = $e;
            $profits[] = $i - $e;
        }

        return [
            'labels' => $monthLabels,
            'sales' => $sales,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'profits' => $profits,
        ];
    }
}
