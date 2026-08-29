<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Income;
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

class ReportTest extends TestCase
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

    private function createConfirmedSale(): void
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
                ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 100000],
            ],
        ]);

        $sale = Sale::latest('id')->firstOrFail();

        $this->actingAs($this->admin)->post("/sales/{$sale->id}/confirm");
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/reports/sales')->assertRedirect('/login');
        $this->get('/reports/inventory')->assertRedirect('/login');
        $this->get('/reports/financial')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/reports')->assertForbidden();
        $this->actingAs($user)->get('/reports/sales')->assertForbidden();
    }

    public function test_reports_index_is_accessible(): void
    {
        $this->actingAs($this->admin)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Ventas')
            ->assertSee('Inventario')
            ->assertSee('Financiero');
    }

    public function test_sales_report_downloads_valid_pdf(): void
    {
        $this->createConfirmedSale();

        $this->actingAs($this->admin)
            ->get('/reports/sales?format=pdf')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_sales_report_downloads_valid_csv(): void
    {
        $this->createConfirmedSale();

        $this->actingAs($this->admin)
            ->get('/reports/sales?format=csv')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_inventory_report_downloads_valid_pdf(): void
    {
        $this->createProductWithStock();

        $this->actingAs($this->admin)
            ->get('/reports/inventory?format=pdf')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_inventory_report_downloads_valid_csv(): void
    {
        $this->createProductWithStock();

        $this->actingAs($this->admin)
            ->get('/reports/inventory?format=csv')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_financial_report_downloads_valid_pdf(): void
    {
        Income::factory()->create(['amount' => 1000000, 'income_date' => now()->toDateString()]);
        Expense::factory()->create(['amount' => 400000, 'expense_date' => now()->toDateString()]);

        $this->actingAs($this->admin)
            ->get('/reports/financial?format=pdf')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_financial_report_downloads_valid_csv(): void
    {
        Income::factory()->create(['amount' => 1000000, 'income_date' => now()->toDateString()]);
        Expense::factory()->create(['amount' => 400000, 'expense_date' => now()->toDateString()]);

        $this->actingAs($this->admin)
            ->get('/reports/financial?format=csv')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
