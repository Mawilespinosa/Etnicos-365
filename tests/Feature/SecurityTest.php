<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
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

    /**
     * Every protected route must redirect guests to the login page.
     */
    public function test_all_protected_routes_redirect_guests_to_login(): void
    {
        $protectedRoutes = [
            '/dashboard',
            '/users',
            '/users/create',
            '/roles',
            '/roles/create',
            '/sellers',
            '/sellers/create',
            '/clients',
            '/clients/create',
            '/suppliers',
            '/suppliers/create',
            '/products',
            '/products/create',
            '/raw-materials',
            '/raw-materials/create',
            '/production/orders',
            '/production/orders/create',
            '/inventory',
            '/inventory/movements',
            '/inventory/alerts',
            '/sales',
            '/sales/create',
            '/finances',
            '/reports',
            '/reports/sales',
            '/reports/inventory',
            '/reports/financial',
        ];

        foreach ($protectedRoutes as $route) {
            $this->get($route)->assertRedirect('/login');
        }
    }

    /**
     * An authenticated user without the required permission must get 403.
     */
    public function test_user_without_permission_gets_403_on_all_modules(): void
    {
        $user = User::factory()->create();

        $forbiddenRoutes = [
            '/dashboard',
            '/users',
            '/roles',
            '/sellers',
            '/clients',
            '/suppliers',
            '/products',
            '/raw-materials',
            '/production/orders',
            '/inventory',
            '/sales',
            '/finances',
            '/reports',
        ];

        foreach ($forbiddenRoutes as $route) {
            $this->actingAs($user)->get($route)->assertForbidden();
        }
    }

    /**
     * Invalid inputs must be rejected with server-side validation errors.
     */
    public function test_invalid_inputs_are_rejected_with_validation_errors(): void
    {
        $this->actingAs($this->admin)
            ->post('/sellers', ['name' => '', 'document_number' => ''])
            ->assertSessionHasErrors(['name', 'document_number']);

        $this->actingAs($this->admin)
            ->post('/products', ['code' => '', 'name' => '', 'cost' => -1, 'price' => -1])
            ->assertSessionHasErrors(['code', 'name', 'cost', 'price']);

        $this->actingAs($this->admin)
            ->post('/production/orders', ['product_id' => 9999, 'quantity' => 0])
            ->assertSessionHasErrors(['product_id', 'quantity']);

        $this->actingAs($this->admin)
            ->post('/inventory/movements', [
                'product_id' => 9999,
                'type' => 'invalid',
                'quantity' => 0,
                'reason' => '',
            ])
            ->assertSessionHasErrors(['product_id', 'type', 'quantity', 'reason']);

        $this->actingAs($this->admin)
            ->post('/finances/incomes', [
                'description' => '',
                'amount' => 0,
                'income_date' => 'not-a-date',
            ])
            ->assertSessionHasErrors(['description', 'amount', 'income_date']);
    }

    /**
     * A sale cannot be created with an injected status/payment_status.
     */
    public function test_mass_assignment_cannot_set_sale_status(): void
    {
        $client = Client::factory()->create();
        $product = Product::factory()->create(['stock_qty' => 100, 'min_stock' => 10]);

        $this->actingAs($this->admin)
            ->post('/sales', [
                'client_id' => $client->id,
                'seller_id' => null,
                'sale_date' => now()->toDateString(),
                'discount' => 0,
                'payment_amount' => 0,
                'payment_method' => 'cash',
                'notes' => null,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000],
                ],
            ])
            ->assertRedirect();

        $sale = Sale::latest('id')->firstOrFail();

        $this->assertSame('draft', $sale->status);
        $this->assertSame('pending', $sale->payment_status);
    }

    /**
     * A production order cannot be created with an injected status/current_stage.
     */
    public function test_mass_assignment_cannot_set_production_order_status(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->post('/production/orders', [
                'product_id' => $product->id,
                'quantity' => 100,
                'notes' => 'Prueba',
                'status' => 'completed',
                'current_stage' => 8,
            ])
            ->assertRedirect();

        $order = ProductionOrder::latest('id')->firstOrFail();

        $this->assertSame('pending', $order->status);
        $this->assertSame(1, $order->current_stage);
    }

    /**
     * Roles are only assigned through the explicit sync, never via mass assignment.
     */
    public function test_mass_assignment_cannot_assign_roles_implicitly(): void
    {
        $role = Role::where('name', 'sales')->firstOrFail();

        $this->actingAs($this->admin)
            ->post('/users', [
                'name' => 'Usuario Prueba',
                'email' => 'prueba@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'roles' => [$role->id],
            ])
            ->assertRedirect();

        $user = User::where('email', 'prueba@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('sales'));

        // A second user created without the roles key must not get any role.
        $this->actingAs($this->admin)
            ->post('/users', [
                'name' => 'Usuario Sin Rol',
                'email' => 'sinrol@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->assertRedirect();

        $noRoleUser = User::where('email', 'sinrol@example.com')->firstOrFail();
        $this->assertCount(0, $noRoleUser->roles);
    }

    /**
     * A user cannot delete their own account (policy).
     */
    public function test_admin_cannot_delete_own_account(): void
    {
        $this->actingAs($this->admin)
            ->delete("/users/{$this->admin->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /**
     * The built-in admin role cannot be deleted (policy).
     */
    public function test_admin_role_cannot_be_deleted(): void
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        $this->actingAs($this->admin)
            ->delete("/roles/{$adminRole->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('roles', ['id' => $adminRole->id]);
    }

    /**
     * A role that still has users assigned cannot be deleted.
     */
    public function test_role_with_users_cannot_be_deleted(): void
    {
        $salesRole = Role::where('name', 'sales')->firstOrFail();
        $user = User::factory()->create();
        $user->roles()->attach($salesRole);

        $this->actingAs($this->admin)
            ->delete("/roles/{$salesRole->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['id' => $salesRole->id]);
    }

    /**
     * The login form must include the CSRF token.
     */
    public function test_login_form_contains_csrf_token(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('_token', false);
    }

    /**
     * User-provided values must be HTML-escaped in Blade views (XSS protection).
     */
    public function test_user_input_is_escaped_in_views(): void
    {
        $client = Client::factory()->create(['name' => '<script>alert("xss")</script>']);

        $this->actingAs($this->admin)
            ->get('/clients')
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("xss")</script>', false);
    }

    /**
     * The login endpoint is rate limited (5 attempts per minute).
     */
    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertStatus(429);
    }

    /**
     * Report date filters must be validated (no Carbon exceptions on bad input).
     */
    public function test_report_rejects_invalid_date_filters(): void
    {
        $this->actingAs($this->admin)
            ->get('/reports/sales?date_from=not-a-date')
            ->assertSessionHasErrors('date_from');

        $this->actingAs($this->admin)
            ->get('/reports/financial?date_to=not-a-date')
            ->assertSessionHasErrors('date_to');
    }

    /**
     * Finance date filters must be validated.
     */
    public function test_finances_rejects_invalid_date_filters(): void
    {
        $this->actingAs($this->admin)
            ->get('/finances?date_from=not-a-date')
            ->assertSessionHasErrors('date_from');
    }
}