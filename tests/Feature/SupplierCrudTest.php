<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
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

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/suppliers')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/suppliers')->assertForbidden();
    }

    public function test_admin_can_list_suppliers(): void
    {
        Supplier::factory()->create(['name' => 'Proveedor Demo']);

        $this->actingAs($this->admin)
            ->get('/suppliers')
            ->assertOk()
            ->assertSee('Proveedor Demo');
    }

    public function test_admin_can_search_suppliers(): void
    {
        Supplier::factory()->create(['name' => 'Textiles del Valle', 'document_number' => '890100001']);
        Supplier::factory()->create(['name' => 'Insumos Jeans', 'document_number' => '890200002']);

        $this->actingAs($this->admin)
            ->get('/suppliers?search=Textiles')
            ->assertOk()
            ->assertSee('Textiles del Valle')
            ->assertDontSee('Insumos Jeans');
    }

    public function test_admin_can_create_supplier(): void
    {
        $this->actingAs($this->admin)->post('/suppliers', [
            'name' => 'Nuevo Proveedor',
            'document_type' => 'NIT',
            'document_number' => '890400004',
            'phone' => '6015557788',
            'email' => 'proveedor@example.com',
            'contact_name' => 'Juan Pérez',
            'is_active' => 1,
        ])->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', ['document_number' => '890400004']);
    }

    public function test_validation_rejects_duplicate_document_number(): void
    {
        Supplier::factory()->create(['document_number' => '890400004']);

        $this->actingAs($this->admin)
            ->post('/suppliers', [
                'name' => 'Otro Proveedor',
                'document_type' => 'NIT',
                'document_number' => '890400004',
            ])
            ->assertSessionHasErrors('document_number');
    }

    public function test_admin_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Nombre Antiguo']);

        $this->actingAs($this->admin)
            ->put("/suppliers/{$supplier->id}", [
                'name' => 'Nombre Nuevo',
                'document_type' => $supplier->document_type,
                'document_number' => $supplier->document_number,
                'is_active' => 1,
            ])
            ->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Nombre Nuevo']);
    }

    public function test_admin_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/suppliers/{$supplier->id}")
            ->assertRedirect('/suppliers');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}