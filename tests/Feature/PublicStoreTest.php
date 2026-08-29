<?php

namespace Tests\Feature;

use App\Models\Income;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }

    private function createProductWithStock(array $attributes = []): Product
    {
        $product = Product::factory()->create(array_merge([
            'is_active' => true,
            'stock_qty' => 10,
            'min_stock' => 2,
            'price' => 100000,
        ], $attributes));

        Inventory::create([
            'product_id' => $product->id,
            'location' => 'Bodega principal',
            'stock_qty' => $product->stock_qty,
            'min_stock' => $product->min_stock,
        ]);

        return $product;
    }

    private function createInactiveProduct(array $attributes = []): Product
    {
        $product = Product::factory()->create(array_merge([
            'is_active' => false,
            'stock_qty' => 10,
            'price' => 100000,
        ], $attributes));

        Inventory::create([
            'product_id' => $product->id,
            'location' => 'Bodega principal',
            'stock_qty' => $product->stock_qty,
            'min_stock' => $product->min_stock,
        ]);

        return $product;
    }

    private function createOutOfStockProduct(array $attributes = []): Product
    {
        $product = Product::factory()->create(array_merge([
            'is_active' => true,
            'stock_qty' => 0,
            'price' => 100000,
        ], $attributes));

        Inventory::create([
            'product_id' => $product->id,
            'location' => 'Bodega principal',
            'stock_qty' => 0,
            'min_stock' => $product->min_stock,
        ]);

        return $product;
    }

    public function test_catalog_shows_only_active_products_with_stock(): void
    {
        $activeWithStock = $this->createProductWithStock(['name' => 'Jean Activo']);
        $inactive = $this->createInactiveProduct(['name' => 'Jean Inactivo']);
        $outOfStock = $this->createOutOfStockProduct(['name' => 'Jean Agotado']);

        $this->get('/tienda')
            ->assertOk()
            ->assertSee('Jean Activo')
            ->assertDontSee('Jean Inactivo')
            ->assertDontSee('Jean Agotado');
    }

    public function test_catalog_search_works(): void
    {
        $this->createProductWithStock(['name' => 'Jean Clásico', 'code' => 'JC-001']);
        $this->createProductWithStock(['name' => 'Jean Slim', 'code' => 'JS-002']);

        $this->get('/tienda?search=Clásico')
            ->assertOk()
            ->assertSee('Jean Clásico')
            ->assertDontSee('Jean Slim');
    }

    public function test_product_detail_shows_active_product_with_stock(): void
    {
        $product = $this->createProductWithStock(['name' => 'Jean Detalle']);

        $this->get("/tienda/{$product->id}")
            ->assertOk()
            ->assertSee('Jean Detalle')
            ->assertSee('$100.000');
    }

    public function test_product_detail_returns_404_for_inactive_product(): void
    {
        $product = $this->createInactiveProduct(['name' => 'Jean Inactivo']);

        $this->get("/tienda/{$product->id}")
            ->assertNotFound();
    }

    public function test_product_detail_returns_404_for_out_of_stock_product(): void
    {
        $product = $this->createOutOfStockProduct(['name' => 'Jean Agotado']);

        $this->get("/tienda/{$product->id}")
            ->assertNotFound();
    }

    public function test_add_to_cart_respects_stock_limit(): void
    {
        $product = $this->createProductWithStock(['stock_qty' => 5]);

        // Add within stock limit
        $this->post('/tienda/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertRedirect()
         ->assertSessionHas('success');

        $this->assertEquals(3, session('cart')[$product->id]);

        // Try to add more than available stock
        $this->post('/tienda/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertRedirect()
         ->assertSessionHas('error');

        // Cart should still have only 3 (not 6)
        $this->assertEquals(3, session('cart')[$product->id]);
    }

    public function test_add_to_cart_rejects_inactive_product(): void
    {
        $product = $this->createInactiveProduct();

        $this->post('/tienda/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect()
         ->assertSessionHas('error');
    }

    public function test_add_to_cart_rejects_out_of_stock_product(): void
    {
        $product = $this->createOutOfStockProduct();

        $this->post('/tienda/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect()
         ->assertSessionHas('error');
    }

    public function test_cart_shows_items_with_correct_totals(): void
    {
        $product1 = $this->createProductWithStock(['name' => 'Jean A', 'price' => 100000, 'stock_qty' => 10]);
        $product2 = $this->createProductWithStock(['name' => 'Jean B', 'price' => 150000, 'stock_qty' => 5]);

        session(['cart' => [
            $product1->id => 2,
            $product2->id => 1,
        ]]);

        $this->get('/tienda/cart')
            ->assertOk()
            ->assertSee('Jean A')
            ->assertSee('Jean B')
            ->assertSee('$200.000') // 2 * 100000
            ->assertSee('$150.000') // 1 * 150000
            ->assertSee('$350.000'); // Subtotal
    }

    public function test_cart_excludes_inactive_or_out_of_stock_products(): void
    {
        $activeProduct = $this->createProductWithStock(['name' => 'Jean Activo']);
        $inactiveProduct = $this->createInactiveProduct(['name' => 'Jean Inactivo']);
        $outOfStockProduct = $this->createOutOfStockProduct(['name' => 'Jean Agotado']);

        session(['cart' => [
            $activeProduct->id => 1,
            $inactiveProduct->id => 1,
            $outOfStockProduct->id => 1,
        ]]);

        $this->get('/tienda/cart')
            ->assertOk()
            ->assertSee('Jean Activo')
            ->assertDontSee('Jean Inactivo')
            ->assertDontSee('Jean Agotado');
    }

    public function test_update_cart_modifies_quantities(): void
    {
        $product = $this->createProductWithStock(['stock_qty' => 10]);

        session(['cart' => [$product->id => 2]]);

        $this->post('/tienda/cart/update', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ])->assertRedirect()
         ->assertSessionHas('success');

        $this->assertEquals(5, session('cart')[$product->id]);
    }

    public function test_update_cart_removes_item_when_quantity_zero(): void
    {
        $product = $this->createProductWithStock();

        session(['cart' => [$product->id => 2]]);

        $this->post('/tienda/cart/update', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 0],
            ],
        ])->assertRedirect()
         ->assertSessionHas('success');

        $this->assertArrayNotHasKey($product->id, session('cart'));
    }

    public function test_update_cart_respects_stock_limit(): void
    {
        $product = $this->createProductWithStock(['stock_qty' => 3]);

        session(['cart' => [$product->id => 2]]);

        $this->post('/tienda/cart/update', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ])->assertRedirect()
         ->assertSessionHas('success');

        // Should be capped at available stock
        $this->assertEquals(3, session('cart')[$product->id]);
    }

    public function test_checkout_redirects_when_cart_empty(): void
    {
        $this->get('/tienda/checkout')
            ->assertRedirect('/tienda')
            ->assertSessionHas('error');
    }

    public function test_checkout_shows_form_when_cart_has_items(): void
    {
        $product = $this->createProductWithStock();

        session(['cart' => [$product->id => 1]]);

        $this->get('/tienda/checkout')
            ->assertOk()
            ->assertSee('Finalizar compra');
    }

    public function test_process_checkout_creates_sale_with_items_and_pending_payment(): void
    {
        $product = $this->createProductWithStock(['price' => 100000, 'stock_qty' => 10]);

        session(['cart' => [$product->id => 2]]);

        $this->post('/tienda/checkout', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '+57 300 123 4567',
            'address' => 'Calle 123 #45-67',
            'city' => 'Bogotá',
            'notes' => 'Entregar en portería',
        ])->assertRedirect();

        // Verify sale was created
        $sale = Sale::latest('id')->firstOrFail();
        $this->assertSame('confirmed', $sale->status);
        $this->assertSame('pending', $sale->payment_status);
        $this->assertNull($sale->client_id);
        $this->assertNull($sale->seller_id);
        $this->assertSame('Entregar en portería', $sale->notes);

        // Verify sale items
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100000,
            'subtotal' => 200000,
        ]);

        // Verify stock was deducted
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 8,
        ]);
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 8,
        ]);

        // Verify inventory movement
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
        ]);

        // Verify pending PSE payment
        $this->assertDatabaseHas('sale_payments', [
            'sale_id' => $sale->id,
            'amount' => $sale->total,
            'method' => 'pse',
            'user_id' => null,
        ]);

        // Verify NO income created yet (waiting for payment)
        $this->assertDatabaseMissing('incomes', [
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
        ]);

        // Verify redirect to PSE pay page
        $this->assertTrue(str_contains($sale->invoice_number, 'FAC-'));
    }

    public function test_process_checkout_fails_when_insufficient_stock(): void
    {
        $product = $this->createProductWithStock(['stock_qty' => 1]);

        session(['cart' => [$product->id => 5]]);

        $this->post('/tienda/checkout', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '+57 300 123 4567',
            'address' => 'Calle 123',
            'city' => 'Bogotá',
        ])->assertRedirect()
         ->assertSessionHas('error');

        // Sale should not be created
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_pse_pay_shows_payment_page_for_valid_sale(): void
    {
        $product = $this->createProductWithStock();
        session(['cart' => [$product->id => 1]]);

        $this->post('/tienda/checkout', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '+57 300 123 4567',
            'address' => 'Calle 123',
            'city' => 'Bogotá',
        ]);

        $sale = Sale::latest('id')->firstOrFail();

        $this->get("/tienda/pse/pay/{$sale->id}")
            ->assertOk()
            ->assertSee($sale->invoice_number)
            ->assertSee('Pagar con PSE');
    }

    public function test_pse_pay_returns_404_for_non_public_sale(): void
    {
        // Create a regular sale (with client_id)
        $client = \App\Models\Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = Sale::create([
            'invoice_number' => 'FAC-9999',
            'client_id' => $client->id,
            'seller_id' => null,
            'sale_date' => now()->toDateString(),
            'subtotal' => 100000,
            'discount' => 0,
            'tax' => 19000,
            'total' => 119000,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $this->get("/tienda/pse/pay/{$sale->id}")
            ->assertNotFound();
    }

    public function test_pse_callback_marks_payment_as_paid_and_creates_income(): void
    {
        $product = $this->createProductWithStock(['price' => 100000, 'stock_qty' => 10]);
        session(['cart' => [$product->id => 1]]);

        $this->post('/tienda/checkout', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '+57 300 123 4567',
            'address' => 'Calle 123',
            'city' => 'Bogotá',
        ]);

        $sale = Sale::latest('id')->firstOrFail();

        // Simulate PSE callback
        $this->post('/tienda/pse/callback', [
            'sale_id' => $sale->id,
        ])->assertOk()
         ->assertSee('Pago exitoso');

        // Verify payment status updated
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'payment_status' => 'paid',
        ]);

        // Verify income created
        $this->assertDatabaseHas('incomes', [
            'type' => 'sale',
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'amount' => $sale->total,
        ]);

        // Verify cart cleared
        $this->assertEmpty(session('cart'));
    }

    public function test_pse_callback_fails_for_invalid_sale(): void
    {
        $this->post('/tienda/pse/callback', [
            'sale_id' => 99999,
        ])->assertOk()
         ->assertSee('Venta no encontrada');
    }

    public function test_pse_callback_fails_for_non_pending_sale(): void
    {
        $client = \App\Models\Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = Sale::create([
            'invoice_number' => 'FAC-9999',
            'client_id' => $client->id,
            'seller_id' => null,
            'sale_date' => now()->toDateString(),
            'subtotal' => 100000,
            'discount' => 0,
            'tax' => 19000,
            'total' => 119000,
            'status' => 'confirmed',
            'payment_status' => 'paid', // Already paid
        ]);

        $this->post('/tienda/pse/callback', [
            'sale_id' => $sale->id,
        ])->assertOk()
         ->assertSee('Venta no válida');
    }

    public function test_unauthenticated_user_can_access_store_routes(): void
    {
        $product = $this->createProductWithStock();

        $this->get('/tienda')->assertOk();
        $this->get("/tienda/{$product->id}")->assertOk();
        $this->get('/tienda/cart')->assertOk();
        $this->get('/tienda/checkout')->assertOk(); // Will redirect if cart empty, but accessible
    }

    public function test_cart_persists_across_requests(): void
    {
        $product = $this->createProductWithStock();

        $this->post('/tienda/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->get('/tienda/cart')
            ->assertOk()
            ->assertSee($product->name);

        $this->get('/tienda')
            ->assertOk();

        $this->get('/tienda/cart')
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_checkout_validates_required_fields(): void
    {
        $product = $this->createProductWithStock();
        session(['cart' => [$product->id => 1]]);

        $this->post('/tienda/checkout', [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => '',
            'address' => '',
            'city' => '',
        ])->assertSessionHasErrors(['name', 'email', 'phone', 'address', 'city']);
    }

    public function test_invoice_number_is_autoincremental_in_public_store(): void
    {
        $product = $this->createProductWithStock(['stock_qty' => 10]);

        // First order
        session(['cart' => [$product->id => 1]]);
        $this->post('/tienda/checkout', [
            'name' => 'Cliente 1',
            'email' => 'cliente1@example.com',
            'phone' => '+57 300 111 1111',
            'address' => 'Dirección 1',
            'city' => 'Bogotá',
        ]);
        $sale1 = Sale::latest('id')->firstOrFail();

        // Second order
        session(['cart' => [$product->id => 1]]);
        $this->post('/tienda/checkout', [
            'name' => 'Cliente 2',
            'email' => 'cliente2@example.com',
            'phone' => '+57 300 222 2222',
            'address' => 'Dirección 2',
            'city' => 'Medellín',
        ]);
        $sale2 = Sale::latest('id')->firstOrFail();

        $this->assertSame('FAC-0001', $sale1->invoice_number);
        $this->assertSame('FAC-0002', $sale2->invoice_number);
    }

    public function test_tax_calculation_is_correct(): void
    {
        $product = $this->createProductWithStock(['price' => 100000, 'stock_qty' => 10]);

        session(['cart' => [$product->id => 1]]);

        $this->post('/tienda/checkout', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '+57 300 123 4567',
            'address' => 'Calle 123',
            'city' => 'Bogotá',
        ]);

        $sale = Sale::latest('id')->firstOrFail();

        // subtotal = 100000, tax = 19000 (19%), total = 119000
        $this->assertSame('100000.00', $sale->subtotal);
        $this->assertSame('19000.00', $sale->tax);
        $this->assertSame('119000.00', $sale->total);
    }
}