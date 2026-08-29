<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionOrderRequest;
use App\Http\Requests\UpdateProductionOrderRequest;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductionOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = ProductionOrder::query()
            ->with('product')
            ->when($request->search, function ($query, string $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($pq) use ($search): void {
                            $pq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->status, function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('production.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('production.orders.create', compact('products'));
    }

    public function store(StoreProductionOrderRequest $request): RedirectResponse
    {
        $order = DB::transaction(function () use ($request): ProductionOrder {
            $order = ProductionOrder::create([
                'code' => $this->nextCode(),
                'product_id' => $request->validated('product_id'),
                'quantity' => $request->validated('quantity'),
                'current_stage' => 1,
                'status' => 'pending',
                'notes' => $request->validated('notes'),
                'created_by' => auth()->id(),
            ]);

            foreach (config('production.stages') as $stage) {
                $order->stages()->create([
                    'stage_number' => $stage['order'],
                    'name' => $stage['label'],
                    'status' => 'pending',
                ]);
            }

            return $order;
        });

        return redirect()->route('production.orders.show', $order)
            ->with('success', 'Orden de producción creada correctamente.');
    }

    public function show(ProductionOrder $order): View
    {
        $order->load(['product', 'stages', 'creator']);

        return view('production.orders.show', compact('order'));
    }

    public function edit(ProductionOrder $order): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('production.orders.edit', compact('order', 'products'));
    }

    public function update(UpdateProductionOrderRequest $request, ProductionOrder $order): RedirectResponse
    {
        if ($order->status !== 'pending' || $order->current_stage !== 1) {
            return back()->with('error', 'Solo se pueden editar órdenes de producción pendientes que no han iniciado producción.');
        }

        $order->update($request->validated());

        return redirect()->route('production.orders.index')
            ->with('success', 'Orden de producción actualizada correctamente.');
    }

    public function destroy(ProductionOrder $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('production.orders.index')
            ->with('success', 'Orden de producción eliminada correctamente.');
    }

    public function cancel(ProductionOrder $order): RedirectResponse
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'No se puede cancelar una orden de producción completada.');
        }

        if ($order->status === 'cancelled') {
            return back()->with('error', 'La orden de producción ya está cancelada.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Orden de producción cancelada.');
    }

    public function advance(ProductionOrder $order): RedirectResponse
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'La orden de producción ya está completada.');
        }

        if ($order->status === 'cancelled') {
            return back()->with('error', 'No se puede avanzar una orden de producción cancelada.');
        }

        $currentStage = $order->stages()->where('stage_number', $order->current_stage)->first();

        if (! $currentStage) {
            return back()->with('error', 'No se encontró la etapa actual de la orden.');
        }

        if ($currentStage->status === 'completed') {
            return back()->with('error', 'La etapa actual ya fue completada.');
        }

        DB::transaction(function () use ($order, $currentStage): void {
            $currentStage->update([
                'status' => 'completed',
                'completed_by' => auth()->id(),
                'completed_at' => now(),
            ]);

            if ($currentStage->stage_number === (int) config('production.warehouse_stage')) {
                $this->registerWarehouseEntry($order);
            }

            if ($currentStage->stage_number === (int) config('production.distribution_stage')) {
                $order->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                return;
            }

            $nextStage = $currentStage->stage_number + 1;

            $order->update([
                'current_stage' => $nextStage,
                'status' => $nextStage === (int) config('production.distribution_stage')
                    ? 'in_progress'
                    : $order->status,
            ]);
        });

        return back()->with('success', 'Etapa completada correctamente.');
    }

    /**
     * Generate the next sequential order code (OT-0001, OT-0002, ...).
     */
    private function nextCode(): string
    {
        $lastCode = ProductionOrder::query()->orderByDesc('id')->value('code');
        $nextNumber = $lastCode ? ((int) substr($lastCode, 3)) + 1 : 1;

        return 'OT-'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Register the finished-goods entry when the warehouse stage is completed.
     */
    private function registerWarehouseEntry(ProductionOrder $order): void
    {
        $inventory = Inventory::firstOrNew(['product_id' => $order->product_id]);

        if (! $inventory->exists) {
            $inventory->min_stock = $order->product->min_stock ?? 0;
        }

        $inventory->stock_qty = ($inventory->stock_qty ?? 0) + $order->quantity;
        $inventory->save();

        $order->product()->increment('stock_qty', $order->quantity);

        InventoryMovement::create([
            'product_id' => $order->product_id,
            'type' => 'in',
            'quantity' => $order->quantity,
            'reference_type' => ProductionOrder::class,
            'reference_id' => $order->id,
            'reason' => 'Entrada por producción — etapa Bodega',
            'user_id' => auth()->id(),
        ]);
    }
}