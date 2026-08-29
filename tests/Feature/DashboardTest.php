<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    private function createProductWithStock(float $stock = 100, float $minStock = 10): Product
    {
        $product = Product::factory()->create([
            'stock_qty' => $stock,
            'min_stock' => $minStock,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'location' => 'Bodega principal',
            'stock_qty' => $stock,
            'min_stock' => $minStock,
        ]);

        return $product;
    }

    private function createConfirmedSaleToday(): void
    {
        $client = Client::factory()->create();
        $product = $this->createProductWithStock(100, 10);

        $this->actingAs($this->admin)->post('/sales', [
            'client_id' => $client->id,
            'seller_id' => null,
            'sale_date' => now()->toDateString(),
            'discount' => 0,
            'payment_amount' => 0,
            'payment_method' => 'cash',
            'notes' => null,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
            ],
        ]);

        $sale = Sale::latest('id')->firstOrFail();

        $this->actingAs($this->admin)->post("/sales/{$sale->id}/confirm");
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
    }

    public function test_dashboard_shows_kpis_with_real_data(): void
    {
        $this->createConfirmedSaleToday();

        ProductionOrder::factory()->create(['status' => 'pending']);
        ProductionOrder::factory()->create(['status' => 'in_progress']);
        ProductionOrder::factory()->create(['status' => 'completed']);

        $this->createProductWithStock(5, 10);

        Income::factory()->create(['amount' => 500000, 'income_date' => now()->toDateString()]);
        Expense::factory()->create(['amount' => 200000, 'expense_date' => now()->toDateString()]);

        $this->actingAs($this->admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('119.000')   // ventas del día (1 x 100000 + 19% IVA)
            ->assertSee('119.000')   // ventas del mes
            ->assertSee('2')         // OT activas (pending + in_progress)
            ->assertSee('1')         // producto con stock bajo
            ->assertSee('619.000')   // ingresos del mes (119.000 venta + 500.000 manual)
            ->assertSee('200.000')   // egresos del mes
            ->assertSee('419.000');  // utilidad del mes
    }
}
