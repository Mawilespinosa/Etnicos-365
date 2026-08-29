<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryMovementRequest;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $inventory = Inventory::query()
            ->with('product')
            ->when($request->search, function ($query, string $search): void {
                $query->whereHas('product', function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('stock_qty')
            ->paginate(10)
            ->withQueryString();

        return view('inventory.index', compact('inventory'));
    }

    public function movements(Request $request): View
    {
        $movements = InventoryMovement::query()
            ->with(['product', 'user'])
            ->when($request->product_id, function ($query, int $productId): void {
                $query->where('product_id', $productId);
            })
            ->when($request->type, function ($query, string $type): void {
                $query->where('type', $type);
            })
            ->when($request->date_from, function ($query, string $from): void {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($request->date_to, function ($query, string $to): void {
                $query->whereDate('created_at', '<=', $to);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'products'));
    }

    public function storeMovement(StoreInventoryMovementRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $type = $validated['type'];
        $quantity = (float) $validated['quantity'];

        try {
            DB::transaction(function () use ($validated, $type, $quantity): void {
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
                $inventory = Inventory::lockForUpdate()->firstOrNew(['product_id' => $product->id]);

                if (! $inventory->exists) {
                    $inventory->min_stock = $product->min_stock ?? 0;
                    $inventory->stock_qty = $product->stock_qty ?? 0;
                }

                $delta = match ($type) {
                    'in' => $quantity,
                    'out' => -$quantity,
                    'adjustment' => $quantity,
                };

                $newInventoryStock = ($inventory->stock_qty ?? 0) + $delta;
                $newProductStock = ($product->stock_qty ?? 0) + $delta;

                if ($newInventoryStock < 0 || $newProductStock < 0) {
                    throw new RuntimeException('El stock no puede quedar negativo.');
                }

                $inventory->stock_qty = $newInventoryStock;
                $inventory->save();

                $product->stock_qty = $newProductStock;
                $product->save();

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'reason' => $validated['reason'],
                    'user_id' => auth()->id(),
                ]);
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('inventory.movements')
            ->with('success', 'Movimiento de inventario registrado correctamente.');
    }

    public function alerts(): View
    {
        $alerts = Product::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereColumn('stock_qty', '<=', 'min_stock')
                    ->orWhereHas('inventory', function ($q): void {
                        $q->whereColumn('stock_qty', '<=', 'min_stock');
                    });
            })
            ->with('inventory')
            ->orderBy('name')
            ->paginate(10);

        return view('inventory.alerts', compact('alerts'));
    }
}