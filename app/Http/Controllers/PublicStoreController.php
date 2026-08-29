<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Income;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Contracts\View\View as ViewContract;

class PublicStoreController extends Controller
{
    /**
     * Display the public catalog with search and pagination.
     * Only shows products that are active and have stock > 0.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('stock_qty', '>', 0)
            ->when($request->search, function ($query, string $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('store.index', compact('products'));
    }

    /**
     * Display the product detail page.
     */
    public function show(Product $product): View
    {
        // Ensure product is active and has stock
        if (! $product->is_active || $product->stock_qty <= 0) {
            abort(404, 'Producto no disponible.');
        }

        return view('store.show', compact('product'));
    }

    /**
     * Add a product to the cart (session).
     */
    public function addToCart(StoreCartRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->validated('product_id'));
        $quantity = $request->validated('quantity');

        // Check if product is available
        if (! $product->is_active || $product->stock_qty <= 0) {
            return back()->with('error', 'Este producto no está disponible.');
        }

        // Check stock availability
        $cart = session()->get('cart', []);
        $currentInCart = $cart[$product->id] ?? 0;
        $availableStock = (float) $product->stock_qty - $currentInCart;

        if ($quantity > $availableStock) {
            return back()->with('error', "Stock insuficiente. Disponible: {$availableStock}");
        }

        $cart[$product->id] = $currentInCart + $quantity;
        session()->put('cart', $cart);

        return back()->with('success', 'Producto agregado al carrito.');
    }

    /**
     * Display the cart contents.
     */
    public function cart(): View
    {
        $cart = session()->get('cart', []);
        $items = [];
        $subtotal = 0.0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);

            if (! $product || ! $product->is_active) {
                continue;
            }

            $availableQty = min((float) $quantity, (float) $product->stock_qty);
            if ($availableQty <= 0) {
                continue;
            }

            $lineSubtotal = round($availableQty * (float) $product->price, 2);
            $subtotal += $lineSubtotal;

            $items[] = [
                'product' => $product,
                'quantity' => $availableQty,
                'subtotal' => $lineSubtotal,
            ];
        }

        $taxRate = config('sales.tax_rate', 0.19);
        $tax = round($subtotal * $taxRate, 2);
        $total = round($subtotal + $tax, 2);

        return view('store.cart', compact('items', 'subtotal', 'tax', 'total'));
    }

    /**
     * Update cart quantities or remove items.
     */
    public function updateCart(UpdateCartRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newCart = [];

        foreach ($validated['items'] as $item) {
            $productId = $item['product_id'];
            $quantity = (int) $item['quantity'];

            if ($quantity > 0) {
                $product = Product::find($productId);

                if ($product && $product->is_active && $product->stock_qty > 0) {
                    $availableQty = min($quantity, (float) $product->stock_qty);
                    if ($availableQty > 0) {
                        $newCart[$productId] = $availableQty;
                    }
                }
            }
        }

        session()->put('cart', $newCart);

        return redirect()->route('store.cart')->with('success', 'Carrito actualizado.');
    }

    /**
     * Display the checkout form (guest).
     */
    public function checkout(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.index')->with('error', 'El carrito está vacío.');
        }

        // Verify all items still have stock and build items array
        $items = [];
        $subtotal = 0.0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);

            if (! $product || ! $product->is_active || $product->stock_qty <= 0) {
                return redirect()->route('store.cart')->with('error', 'Algunos productos ya no están disponibles.');
            }

            $availableQty = min((float) $quantity, (float) $product->stock_qty);
            if ($availableQty <= 0) {
                return redirect()->route('store.cart')->with('error', 'Algunos productos ya no tienen stock disponible.');
            }

            $lineSubtotal = round($availableQty * (float) $product->price, 2);
            $subtotal += $lineSubtotal;

            $items[] = [
                'product' => $product,
                'quantity' => $availableQty,
                'subtotal' => $lineSubtotal,
            ];
        }

        $taxRate = config('sales.tax_rate', 0.19);
        $tax = round($subtotal * $taxRate, 2);
        $total = round($subtotal + $tax, 2);

        return view('store.checkout', compact('items', 'subtotal', 'tax', 'total'));
    }

    /**
     * Process the checkout: create sale, items, payment (pending), deduct stock.
     */
    public function processCheckout(CheckoutRequest $request): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.index')->with('error', 'El carrito está vacío.');
        }

        try {
            $sale = DB::transaction(function () use ($request, $cart): Sale {
                // Calculate totals
                $subtotal = 0.0;
                $saleItems = [];

                foreach ($cart as $productId => $quantity) {
                    $product = Product::findOrFail($productId);

                    if (! $product->is_active || $product->stock_qty <= 0) {
                        throw new \RuntimeException("El producto {$product->name} ya no está disponible.");
                    }

                    $availableQty = min((float) $quantity, (float) $product->stock_qty);
                    if ($availableQty <= 0) {
                        throw new \RuntimeException("Stock insuficiente para {$product->name}.");
                    }

                    $lineSubtotal = round($availableQty * (float) $product->price, 2);
                    $subtotal += $lineSubtotal;

                    $saleItems[] = [
                        'product' => $product,
                        'quantity' => $availableQty,
                        'unit_price' => $product->price,
                        'subtotal' => $lineSubtotal,
                    ];
                }

                $taxRate = config('sales.tax_rate', 0.19);
                $tax = round($subtotal * $taxRate, 2);
                $total = round($subtotal + $tax, 2);

                // Create the sale (confirmed status, payment pending)
                $sale = Sale::create([
                    'invoice_number' => $this->nextInvoiceNumber(),
                    'client_id' => null, // Guest checkout
                    'seller_id' => null, // No seller for public store
                    'sale_date' => now()->toDateString(),
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'tax' => $tax,
                    'total' => $total,
                    'status' => 'confirmed',
                    'payment_status' => 'pending',
                    'notes' => $request->validated('notes'),
                ]);

                // Create sale items and deduct stock
                foreach ($saleItems as $item) {
                    $product = $item['product'];

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Deduct stock
                    $this->deductStock($product, $item['quantity'], $sale);
                }

                // Create pending PSE payment
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'amount' => $total,
                    'payment_date' => now()->toDateString(),
                    'method' => 'pse',
                    'user_id' => null, // Guest
                    'notes' => 'Pago PSE pendiente',
                ]);

                return $sale;
            });

            // Redirect to PSE payment page
            return redirect()->route('store.pse.pay', $sale);
        } catch (\Throwable $e) {
            // Log the exception for debugging
            \Log::error('Checkout error: ' . get_class($e) . ' - ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the PSE payment placeholder page.
     */
    public function psePay(Sale $sale): View
    {
        // Ensure sale belongs to public store (no client, no seller, payment pending)
        if ($sale->client_id !== null || $sale->seller_id !== null || $sale->payment_status !== 'pending') {
            abort(404);
        }

        $payment = $sale->payments()->where('method', 'pse')->firstOrFail();

        return view('store.pse-pay', compact('sale', 'payment'));
    }

    /**
     * Handle PSE callback (simulated successful payment).
     */
    public function pseCallback(Request $request): View
    {
        $saleId = $request->input('sale_id');

        if (! $saleId) {
            return view('store.pse-callback', [
                'success' => false,
                'message' => 'ID de venta no proporcionado.',
            ]);
        }

        $sale = Sale::find($saleId);

        if (! $sale) {
            return view('store.pse-callback', [
                'success' => false,
                'message' => 'Venta no encontrada.',
            ]);
        }

        // Verify this is a public store sale with pending PSE payment
        if ($sale->client_id !== null || $sale->seller_id !== null || $sale->payment_status !== 'pending') {
            return view('store.pse-callback', [
                'success' => false,
                'message' => 'Venta no válida para callback PSE.',
            ]);
        }

        try {
            DB::transaction(function () use ($sale): void {
                // Update payment to paid
                $payment = $sale->payments()->where('method', 'pse')->firstOrFail();
                $payment->update(['notes' => 'Pago PSE completado (simulado)']);

                // Update sale payment status
                $sale->update(['payment_status' => 'paid']);

                // Create income record (same as SaleController::confirm)
                Income::create([
                    'type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'description' => "Venta {$sale->invoice_number} (Tienda pública)",
                    'amount' => $sale->total,
                    'income_date' => $sale->sale_date,
                    'user_id' => null, // Guest
                ]);
            });

            // Clear cart
            session()->forget('cart');

            return view('store.pse-callback', [
                'success' => true,
                'sale' => $sale->fresh(),
                'message' => 'Pago exitoso. Su pedido ha sido confirmado.',
            ]);
        } catch (\Exception $e) {
            return view('store.pse-callback', [
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate the next sequential invoice number (FAC-0001, FAC-0002, ...).
     */
    private function nextInvoiceNumber(): string
    {
        $last = Sale::query()->orderByDesc('id')->value('invoice_number');
        $next = $last ? ((int) substr($last, strlen(config('sales.invoice_prefix')))) + 1 : 1;

        return config('sales.invoice_prefix') . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Deduct finished-goods stock for a sale item.
     */
    private function deductStock(Product $product, float $quantity, Sale $sale): void
    {
        // Find existing inventory record or create new one
        $inventory = Inventory::where('product_id', $product->id)->first();

        if (! $inventory) {
            $inventory = new Inventory([
                'product_id' => $product->id,
                'location' => 'Bodega principal',
                'min_stock' => $product->min_stock ?? 0,
                'stock_qty' => $product->stock_qty ?? 0,
            ]);
        }

        if (($inventory->stock_qty ?? 0) < $quantity || ($product->stock_qty ?? 0) < $quantity) {
            throw new \RuntimeException("Stock insuficiente para el producto {$product->name}.");
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
            'reason' => "Salida por venta {$sale->invoice_number} (Tienda pública)",
            'user_id' => null, // Guest
        ]);
    }
}