<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
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

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/inventory')->assertRedirect('/login');
        $this->get('/inventory/movements')->assertRedirect('/login');
        $this->get('/inventory/alerts')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/inventory')->assertForbidden();
        $this->actingAs($user)->get('/inventory/movements')->assertForbidden();
        $this->actingAs($user)->get('/inventory/alerts')->assertForbidden();
    }

    public function test_admin_can_list_inventory(): void
    {
        $product = $this->createProductWithStock();

        $this->actingAs($this->admin)
            ->get('/inventory')
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_in_movement_increases_stock_and_creates_movement(): void
    {
        $product = $this->createProductWithStock(50, 10);

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => 25,
                'reason' => 'Compra de reposición',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 75,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 75,
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 25,
            'reason' => 'Compra de reposición',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_out_movement_decreases_stock(): void
    {
        $product = $this->createProductWithStock(50, 10);

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => 20,
                'reason' => 'Salida por venta',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 30,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 30,
        ]);
    }

    public function test_out_movement_cannot_leave_negative_stock(): void
    {
        $product = $this->createProductWithStock(10, 5);

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => 15,
                'reason' => 'Salida mayor al stock',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 10,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 10,
        ]);
        $this->assertDatabaseCount('inventory_movements', 0);
    }

    public function test_adjustment_with_positive_quantity_increases_stock(): void
    {
        $product = $this->createProductWithStock(50, 10);

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => 5,
                'reason' => 'Ajuste por conteo físico',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 55,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 55,
        ]);
    }

    public function test_adjustment_with_negative_quantity_decreases_stock(): void
    {
        $product = $this->createProductWithStock(50, 10);

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => -5,
                'reason' => 'Ajuste por merma',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 45,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 45,
        ]);
    }

    public function test_adjustment_cannot_leave_negative_stock(): void
    {
        $product = $this->createProductWithStock(10, 5);

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => -15,
                'reason' => 'Ajuste excesivo',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'stock_qty' => 10,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_qty' => 10,
        ]);
        $this->assertDatabaseCount('inventory_movements', 0);
    }

    public function test_movement_requires_reason_and_valid_quantity(): void
    {
        $product = $this->createProductWithStock();

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => 0,
                'reason' => '',
            ])
            ->assertSessionHasErrors(['quantity', 'reason']);
    }

    public function test_alerts_show_only_products_below_minimum(): void
    {
        $low = $this->createProductWithStock(5, 10, ['name' => 'Jean Bajo Stock']);
        $ok = $this->createProductWithStock(50, 10, ['name' => 'Jean Stock Normal']);

        $this->actingAs($this->admin)
            ->get('/inventory/alerts')
            ->assertOk()
            ->assertSee($low->name)
            ->assertDontSee($ok->name);
    }

    public function test_movements_can_be_filtered_by_product_type_and_date(): void
    {
        $productA = $this->createProductWithStock(100, 10);
        $productB = $this->createProductWithStock(100, 10);

        $movementA = InventoryMovement::create([
            'product_id' => $productA->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'Entrada filtro A',
            'user_id' => $this->admin->id,
        ]);
        $movementA->created_at = now()->subDays(2);
        $movementA->save();

        InventoryMovement::create([
            'product_id' => $productB->id,
            'type' => 'out',
            'quantity' => 5,
            'reason' => 'Salida filtro B',
            'user_id' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->get('/inventory/movements?product_id='.$productA->id)
            ->assertOk()
            ->assertSee('Entrada filtro A')
            ->assertDontSee('Salida filtro B');

        $this->actingAs($this->admin)
            ->get('/inventory/movements?type=out')
            ->assertOk()
            ->assertSee('Salida filtro B')
            ->assertDontSee('Entrada filtro A');

        $this->actingAs($this->admin)
            ->get('/inventory/movements?date_from='.now()->subDay()->toDateString())
            ->assertOk()
            ->assertSee('Salida filtro B')
            ->assertDontSee('Entrada filtro A');
    }
}