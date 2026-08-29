<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalePaymentRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Client;
use App\Models\Income;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::query()
            ->with(['client', 'seller'])
            ->when($request->search, function ($query, string $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($cq) use ($search): void {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->status, function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->when($request->payment_status, function ($query, string $paymentStatus): void {
                $query->where('payment_status', $paymentStatus);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $sellers = Seller::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('sales.create', compact('clients', 'sellers', 'products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $sale = DB::transaction(function () use ($request): Sale {
            $items = $request->validated('items');

            $subtotal = 0.0;
            foreach ($items as $item) {
                $subtotal += (float) $item['quantity'] * (float) $item['unit_price'];
            }

            $subtotal = round($subtotal, 2);
            $discount = round((float) $request->validated('discount', 0), 2);
            $taxable = $subtotal - $discount;
            $tax = round($taxable * (float) config('sales.tax_rate'), 2);
            $total = round($taxable + $tax, 2);

            $paymentAmount = round((float) $request->validated('payment_amount', 0), 2);
            $paymentStatus = match (true) {
                $paymentAmount >= $total => 'paid',
                $paymentAmount > 0 => 'partial',
                default => 'pending',
            };

            $sale = Sale::create([
                'invoice_number' => $this->nextInvoiceNumber(),
                'client_id' => $request->validated('client_id'),
                'seller_id' => $request->validated('seller_id'),
                'sale_date' => $request->validated('sale_date'),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'status' => 'draft',
                'payment_status' => $paymentStatus,
                'notes' => $request->validated('notes'),
            ]);

            foreach ($items as $item) {
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
                ]);
            }

            if ($paymentAmount > 0) {
                $sale->payments()->create([
                    'amount' => $paymentAmount,
                    'payment_date' => $request->validated('sale_date'),
                    'method' => $request->validated('payment_method', 'cash'),
                    'user_id' => auth()->id(),
                    'notes' => 'Pago inicial',
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Venta creada correctamente. Confirme la venta para descontar inventario y registrar el ingreso.');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['client', 'seller', 'items.product', 'payments.user']);

        return view('sales.show', compact('sale'));
    }

    public function confirm(Sale $sale): RedirectResponse
    {
        if ($sale->status !== 'draft') {
            return back()->with('error', 'Solo se pueden confirmar ventas en borrador.');
        }

        try {
            DB::transaction(function () use ($sale): void {
                $sale->items()->with('product')->get()->each(function (SaleItem $item) use ($sale): void {
                    $this->deductStock($item, $sale);
                });

                $sale->update(['status' => 'confirmed']);

                Income::create([
                    'type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'description' => "Venta {$sale->invoice_number}",
                    'amount' => $sale->total,
                    'income_date' => $sale->sale_date,
                    'user_id' => auth()->id(),
                ]);
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Venta confirmada: inventario descontado e ingreso registrado.');
    }

    public function addPayment(StoreSalePaymentRequest $request, Sale $sale): RedirectResponse
    {
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'No se pueden registrar pagos en una venta cancelada.');
        }

        $amount = round((float) $request->validated('amount'), 2);

        if ($amount > $sale->balance) {
            return back()->withInput()->with('error', 'El pago no puede superar el saldo pendiente de la venta.');
        }

        DB::transaction(function () use ($request, $sale, $amount): void {
            $sale->payments()->create([
                'amount' => $amount,
                'payment_date' => $request->validated('payment_date'),
                'method' => $request->validated('method', 'cash'),
                'user_id' => auth()->id(),
                'notes' => $request->validated('notes'),
            ]);

            $sale->refresh();

            $sale->update([
                'payment_status' => $sale->balance <= 0 ? 'paid' : 'partial',
            ]);
        });

        return back()->with('success', 'Pago registrado correctamente.');
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        if ($sale->status !== 'confirmed') {
            return back()->with('error', 'Solo se pueden cancelar ventas confirmadas.');
        }

        DB::transaction(function () use ($sale): void {
            $sale->items()->with('product')->get()->each(function (SaleItem $item) use ($sale): void {
                $this->restoreStock($item, $sale);
            });

            Income::where('reference_type', Sale::class)
                ->where('reference_id', $sale->id)
                ->delete();

            $sale->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Venta cancelada: inventario restaurado e ingreso anulado.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        if ($sale->status !== 'draft') {
            return back()->with('error', 'Solo se pueden eliminar ventas en borrador.');
        }

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Venta eliminada correctamente.');
    }

    /**
     * Generate the next sequential invoice number (FAC-0001, FAC-0002, ...).
     */
    private function nextInvoiceNumber(): string
    {
        $last = Sale::query()->orderByDesc('id')->value('invoice_number');
        $next = $last ? ((int) substr($last, strlen(config('sales.invoice_prefix')))) + 1 : 1;

        return config('sales.invoice_prefix').str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Deduct finished-goods stock for a sale item (locked rows).
     */
    private function deductStock(SaleItem $item, Sale $sale): void
    {
        $product = Product::lockForUpdate()->findOrFail($item->product_id);
        $inventory = Inventory::lockForUpdate()->firstOrNew(['product_id' => $product->id]);

        if (! $inventory->exists) {
            $inventory->min_stock = $product->min_stock ?? 0;
            $inventory->stock_qty = $product->stock_qty ?? 0;
        }

        $quantity = (float) $item->quantity;

        if (($inventory->stock_qty ?? 0) < $quantity || ($product->stock_qty ?? 0) < $quantity) {
            throw new RuntimeException("Stock insuficiente para el producto {$product->name}.");
        }

        $inventory->stock_qty = ($inventory->stock_qty ?? 0) - $quantity;
        $inventory->save();

        $product->stock_qty = ($product->stock_qty ?? 0) - $quantity;
        $product->save();

        InventoryMovement::create([
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => $quantity,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'reason' => "Salida por venta {$sale->invoice_number}",
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Restore finished-goods stock when a confirmed sale is cancelled.
     */
    private function restoreStock(SaleItem $item, Sale $sale): void
    {
        $product = Product::lockForUpdate()->findOrFail($item->product_id);
        $inventory = Inventory::lockForUpdate()->firstOrNew(['product_id' => $product->id]);

        if (! $inventory->exists) {
            $inventory->min_stock = $product->min_stock ?? 0;
            $inventory->stock_qty = $product->stock_qty ?? 0;
        }

        $quantity = (float) $item->quantity;

        $inventory->stock_qty = ($inventory->stock_qty ?? 0) + $quantity;
        $inventory->save();

        $product->stock_qty = ($product->stock_qty ?? 0) + $quantity;
        $product->save();

        InventoryMovement::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $quantity,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'reason' => "Devolución por cancelación de {$sale->invoice_number}",
            'user_id' => auth()->id(),
        ]);
    }
}