<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);

        $this->admin = User::where('email', 'admin@etnicos365.com')->firstOrFail();
    }

    private function createProductWithStock(float $stock = 100, float $minStock = 10, array $attributes = []): Product
    {
        $product = Product::factory()->create(array_merge([
            'stock_qty' => $stock,
            'min_stock' => $minStock,
        ], $attributes));

        Inventory::create([
            'product_id' => $product->id,
            'location' => 'Bodega principal',
            'stock_qty' => $stock,
            'min_stock' => $minStock,
        ]);

        return $product;
    }

    private function salePayload(Client $client, array $items, array $overrides = []): array
    {
        return array_merge([
            'client_id' => $client->id,
            'seller_id' => null,
            'sale_date' => now()->toDateString(),
            'discount' => 0,
            'payment_amount' => 0,
            'payment_method' => 'cash',
            'notes' => null,
            'items' => $items,
        ], $overrides);
    }

    private function createDraftSale(Client $client, array $items, array $overrides = []): Sale
    {
        $this->actingAs($this->admin)->post('/sales', $this->salePayload($client, $items, $overrides));

        return Sale::latest('id')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/sales')->assertRedirect('/login');
        $this->get('/sales/create')->assertRedirect('/login');
        $this->get('/sales/1')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/sales')->assertForbidden();
        $this->actingAs($user)->get('/sales/create')->assertForbidden();
    }

    public function test_admin_can_list_sales(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)
            ->get('/sales')
            ->assertOk()
            ->assertSee($sale->invoice_number)
            ->assertSee($client->name);
    }

    public function test_sales_can_be_searched_and_filtered(): void
    {
        $clientA = Client::factory()->create(['name' => 'Cliente Alfa']);
        $clientB = Client::factory()->create(['name' => 'Cliente Beta']);
        $product = $this->createProductWithStock();

        $saleA = $this->createDraftSale($clientA, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);
        $saleB = $this->createDraftSale($clientB, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ], ['payment_amount' => 50000]);

        $this->actingAs($this->admin)
            ->get('/sales?search=Alfa')
            ->assertOk()
            ->assertSee($saleA->invoice_number)
            ->assertDontSee($saleB->invoice_number);

        $this->actingAs($this->admin)
            ->get('/sales?payment_status=partial')
            ->assertOk()
            ->assertSee($saleB->invoice_number)
            ->assertDontSee($saleA->invoice_number);

        $this->actingAs($this->admin)
            ->get('/sales?status=draft')
            ->assertOk()
            ->assertSee($saleA->invoice_number);
    }

    public function test_invoice_number_is_autoincremental(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $first = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);
        $second = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);

        $this->assertSame('FAC-0001', $first->invoice_number);
        $this->assertSame('FAC-0002', $second->invoice_number);
    }

    public function test_totals_are_calculated_correctly(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 100000],
        ], ['discount' => 10000]);

        // subtotal = 200000, discount = 10000, taxable = 190000, tax = 36100, total = 226100
        $this->assertSame('200000.00', $sale->subtotal);
        $this->assertSame('10000.00', $sale->discount);
        $this->assertSame('36100.00', $sale->tax);
        $this->assertSame('226100.00', $sale->total);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100000,
            'subtotal' => 200000,
        ]);
    }

    public function test_validation_rejects_invalid_sale(): void
    {
        $this->actingAs($this->admin)
            ->post('/sales', [
                'client_id' => 9999,
                'sale_date' => 'not-a-date',
                'items' => [],
            ])
            ->assertSessionHasErrors(['client_id', 'sale_date', 'items']);
    }

    public function test_discount_cannot_exceed_subtotal(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $this->actingAs($this->admin)
            ->post('/sales', $this->salePayload($client, [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
            ], ['discount' => 200000]))
            ->assertSessionHasErrors(['discount']);
    }

    public function test_payment_amount_cannot_exceed_total(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $this->actingAs($this->admin)
            ->post('/sales', $this->salePayload($client, [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
            ], ['payment_amount' => 500000]))
            ->assertSessionHasErrors(['payment_amount']);
    }

    public function test_full_payment_sets_paid_status(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ], ['payment_amount' => 119000]);

        $this->assertSame('paid', $sale->payment_status);
        $this->assertSame(0.0, $sale->balance);
        $this->assertDatabaseHas('sale_payments', [
            'sale_id' => $sale->id,
            'amount' => 119000,
        ]);
    }

    public function test_partial_payment_sets_partial_status_and_balance(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ], ['payment_amount' => 40000]);

        $this->assertSame('partial', $sale->payment_status);
        $this->assertSame(79000.0, $sale->balance);
    }

    public function test_no_payment_sets_pending_status(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);

        $this->assertSame('pending', $sale->payment_status);
        $this->assertSame(119000.0, $sale->balance);
    }

    public function test_show_displays_invoice(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)
            ->get("/sales/{$sale->id}")
            ->assertOk()
            ->assertSee($sale->invoice_number)
            ->assertSee($client->name)
            ->assertSee($product->name);
    }

    public function test_confirm_deducts_stock_and_creates_income(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock(100, 10);

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/confirm")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'confirmed',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 90,
        ]);
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 90,
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 10,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
        ]);
        $this->assertDatabaseHas('incomes', [
            'type' => 'sale',
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'amount' => $sale->total,
        ]);
    }

    public function test_confirm_fails_when_stock_is_insufficient(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock(5, 5);

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/confirm")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 5,
        ]);
        $this->assertDatabaseCount('inventory_movements', 0);
        $this->assertDatabaseCount('incomes', 0);
    }

    public function test_cannot_confirm_already_confirmed_sale(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)->post("/sales/{$sale->id}/confirm");

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/confirm")
            ->assertSessionHas('error');

        $this->assertDatabaseCount('incomes', 1);
    }

    public function test_cancel_reverts_stock_and_voids_income(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock(100, 10);

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)->post("/sales/{$sale->id}/confirm");

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/cancel")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 100,
        ]);
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 100,
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
        ]);
        $this->assertDatabaseMissing('incomes', [
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
        ]);
    }

    public function test_cannot_cancel_draft_sale(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/cancel")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'draft',
        ]);
    }

    public function test_add_payment_updates_status_and_balance(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ], ['payment_amount' => 40000]);

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/payments", [
                'amount' => 79000,
                'payment_date' => now()->toDateString(),
                'method' => 'transfer',
            ])
            ->assertSessionHas('success');

        $sale->refresh();

        $this->assertSame('paid', $sale->payment_status);
        $this->assertSame(0.0, $sale->balance);
        $this->assertDatabaseHas('sale_payments', [
            'sale_id' => $sale->id,
            'amount' => 79000,
            'method' => 'transfer',
        ]);
    }

    public function test_cannot_pay_more_than_pending_balance(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ], ['payment_amount' => 40000]);

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/payments", [
                'amount' => 80000,
                'payment_date' => now()->toDateString(),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('sale_payments', 1);
    }

    public function test_payment_requires_valid_amount(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)
            ->post("/sales/{$sale->id}/payments", [
                'amount' => 0,
                'payment_date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors(['amount']);
    }

    public function test_destroy_only_deletes_draft_sales(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)
            ->delete("/sales/{$sale->id}")
            ->assertRedirect('/sales');

        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseMissing('sale_items', ['sale_id' => $sale->id]);
    }

    public function test_cannot_delete_confirmed_sale(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock();

        $sale = $this->createDraftSale($client, [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
        ]);

        $this->actingAs($this->admin)->post("/sales/{$sale->id}/confirm");

        $this->actingAs($this->admin)
            ->delete("/sales/{$sale->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('sales', ['id' => $sale->id]);
    }
}