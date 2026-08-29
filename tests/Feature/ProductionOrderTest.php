<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionOrderTest extends TestCase
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

    private function createOrder(array $attributes = []): ProductionOrder
    {
        $product = Product::factory()->create(['stock_qty' => 0, 'min_stock' => 5]);

        $this->actingAs($this->admin)->post('/production/orders', array_merge([
            'product_id' => $product->id,
            'quantity' => 100,
            'notes' => 'Orden de prueba',
        ], $attributes));

        return ProductionOrder::latest('id')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/production/orders')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/production/orders')->assertForbidden();
    }

    public function test_admin_can_list_production_orders(): void
    {
        $order = $this->createOrder();

        $this->actingAs($this->admin)
            ->get('/production/orders')
            ->assertOk()
            ->assertSee($order->code);
    }

    public function test_creating_order_generates_eight_stages_in_fixed_order(): void
    {
        $order = $this->createOrder();

        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
            'code' => 'OT-0001',
            'current_stage' => 1,
            'status' => 'pending',
        ]);

        $this->assertSame(8, $order->stages()->count());

        $expected = [
            1 => 'Compra de tela',
            2 => 'Corte',
            3 => 'Confección',
            4 => 'Pulido',
            5 => 'Lavandería',
            6 => 'Empaque',
            7 => 'Bodega',
            8 => 'Distribución',
        ];

        foreach ($expected as $number => $name) {
            $this->assertDatabaseHas('production_order_stages', [
                'production_order_id' => $order->id,
                'stage_number' => $number,
                'name' => $name,
                'status' => 'pending',
            ]);
        }
    }

    public function test_order_code_is_autoincremental(): void
    {
        $this->createOrder();
        $second = $this->createOrder();

        $this->assertSame('OT-0002', $second->code);
    }

    public function test_validation_rejects_invalid_product_and_non_positive_quantity(): void
    {
        $this->actingAs($this->admin)
            ->post('/production/orders', [
                'product_id' => 9999,
                'quantity' => 0,
            ])
            ->assertSessionHasErrors(['product_id', 'quantity']);
    }

    public function test_advance_completes_only_current_stage_and_moves_one_step(): void
    {
        $order = $this->createOrder();

        $this->actingAs($this->admin)
            ->post("/production/orders/{$order->id}/advance")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('production_order_stages', [
            'production_order_id' => $order->id,
            'stage_number' => 1,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('production_order_stages', [
            'production_order_id' => $order->id,
            'stage_number' => 2,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
            'current_stage' => 2,
            'status' => 'pending',
        ]);
    }

    public function test_cannot_skip_stages(): void
    {
        $order = $this->createOrder();

        // Simulate an attempt to skip: mark stage 1 as completed without advancing current_stage.
        $order->stages()->where('stage_number', 1)->update(['status' => 'completed']);

        $this->actingAs($this->admin)
            ->post("/production/orders/{$order->id}/advance")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
            'current_stage' => 1,
        ]);
    }

    public function test_full_flow_from_stage_one_to_eight(): void
    {
        $order = $this->createOrder();

        for ($i = 1; $i <= 8; $i++) {
            $this->actingAs($this->admin)
                ->post("/production/orders/{$order->id}/advance")
                ->assertSessionHas('success');
        }

        $order->refresh();

        $this->assertSame('completed', $order->status);
        $this->assertNotNull($order->completed_at);
        $this->assertSame(8, $order->stages()->where('status', 'completed')->count());
    }

    public function test_completing_warehouse_stage_increments_inventory_and_creates_movement(): void
    {
        $order = $this->createOrder();

        for ($i = 1; $i <= 7; $i++) {
            $this->actingAs($this->admin)
                ->post("/production/orders/{$order->id}/advance")
                ->assertSessionHas('success');
        }

        $order->refresh();

        $this->assertSame('in_progress', $order->status);
        $this->assertSame(8, $order->current_stage);

        $this->assertDatabaseHas('inventory', [
            'product_id' => $order->product_id,
            'stock_qty' => 100,
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $order->product_id,
            'type' => 'in',
            'quantity' => 100,
            'reference_type' => ProductionOrder::class,
            'reference_id' => $order->id,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $order->product_id,
            'stock_qty' => 100,
        ]);
    }

    public function test_cancel_pending_order(): void
    {
        $order = $this->createOrder();

        $this->actingAs($this->admin)
            ->post("/production/orders/{$order->id}/cancel")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_cancel_completed_order(): void
    {
        $order = $this->createOrder();

        for ($i = 1; $i <= 8; $i++) {
            $this->actingAs($this->admin)
                ->post("/production/orders/{$order->id}/advance");
        }

        $this->actingAs($this->admin)
            ->post("/production/orders/{$order->id}/cancel")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }

    public function test_destroy_deletes_order_and_stages(): void
    {
        $order = $this->createOrder();

        $this->actingAs($this->admin)
            ->delete("/production/orders/{$order->id}")
            ->assertRedirect('/production/orders');

        $this->assertDatabaseMissing('production_orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('production_order_stages', ['production_order_id' => $order->id]);
    }

    public function test_only_pending_orders_can_be_edited(): void
    {
        $order = $this->createOrder();

        $this->actingAs($this->admin)
            ->put("/production/orders/{$order->id}", [
                'product_id' => $order->product_id,
                'quantity' => 150,
                'notes' => 'Actualizada',
            ])
            ->assertRedirect('/production/orders');

        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
            'quantity' => 150,
        ]);

        // Advance once so the order is no longer pending.
        $this->actingAs($this->admin)->post("/production/orders/{$order->id}/advance");

        $this->actingAs($this->admin)
            ->put("/production/orders/{$order->id}", [
                'product_id' => $order->product_id,
                'quantity' => 200,
                'notes' => 'No debería aplicar',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('production_orders', [
            'id' => $order->id,
            'quantity' => 150,
        ]);
    }
}