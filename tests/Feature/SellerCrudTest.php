<?php

namespace Tests\Feature;

use App\Models\Seller;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerCrudTest extends TestCase
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
        $this->get('/sellers')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/sellers')->assertForbidden();
    }

    public function test_admin_can_list_sellers(): void
    {
        Seller::factory()->create(['name' => 'Vendedor Demo']);

        $this->actingAs($this->admin)
            ->get('/sellers')
            ->assertOk()
            ->assertSee('Vendedor Demo');
    }

    public function test_admin_can_search_sellers(): void
    {
        Seller::factory()->create(['name' => 'Carlos Pérez', 'document_number' => '1000000001']);
        Seller::factory()->create(['name' => 'Luisa Gómez', 'document_number' => '1000000002']);

        $this->actingAs($this->admin)
            ->get('/sellers?search=Carlos')
            ->assertOk()
            ->assertSee('Carlos Pérez')
            ->assertDontSee('Luisa Gómez');
    }

    public function test_admin_can_create_seller(): void
    {
        $this->actingAs($this->admin)->post('/sellers', [
            'name' => 'Nuevo Vendedor',
            'document_type' => 'CC',
            'document_number' => '1234567890',
            'phone' => '3101234567',
            'email' => 'nuevo@example.com',
            'city' => 'Bogotá',
            'commission_rate' => 5,
            'is_active' => 1,
        ])->assertRedirect('/sellers');

        $this->assertDatabaseHas('sellers', ['document_number' => '1234567890']);
    }

    public function test_validation_rejects_duplicate_document_number(): void
    {
        Seller::factory()->create(['document_number' => '1234567890']);

        $this->actingAs($this->admin)
            ->post('/sellers', [
                'name' => 'Otro Vendedor',
                'document_type' => 'CC',
                'document_number' => '1234567890',
            ])
            ->assertSessionHasErrors('document_number');
    }

    public function test_admin_can_update_seller(): void
    {
        $seller = Seller::factory()->create(['name' => 'Nombre Antiguo']);

        $this->actingAs($this->admin)
            ->put("/sellers/{$seller->id}", [
                'name' => 'Nombre Nuevo',
                'document_type' => $seller->document_type,
                'document_number' => $seller->document_number,
                'commission_rate' => 7,
                'is_active' => 1,
            ])
            ->assertRedirect('/sellers');

        $this->assertDatabaseHas('sellers', ['id' => $seller->id, 'name' => 'Nombre Nuevo']);
    }

    public function test_admin_can_delete_seller(): void
    {
        $seller = Seller::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/sellers/{$seller->id}")
            ->assertRedirect('/sellers');

        $this->assertDatabaseMissing('sellers', ['id' => $seller->id]);
    }
}