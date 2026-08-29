<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Sale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

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

        return view('dashboard.index', compact(
            'salesToday',
            'salesMonth',
            'activeOrders',
            'lowStock',
            'monthIncome',
            'monthExpense',
            'monthProfit',
        ));
    }
}
