<?php

namespace Tests\Feature;

use App\Models\RawMaterial;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RawMaterialCrudTest extends TestCase
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
        $this->get('/raw-materials')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/raw-materials')->assertForbidden();
    }

    public function test_admin_can_list_raw_materials(): void
    {
        RawMaterial::factory()->create(['name' => 'Tela Demo']);

        $this->actingAs($this->admin)
            ->get('/raw-materials')
            ->assertOk()
            ->assertSee('Tela Demo');
    }

    public function test_admin_can_search_raw_materials(): void
    {
        RawMaterial::factory()->create(['name' => 'Tela Denim', 'code' => 'MAT-001']);
        RawMaterial::factory()->create(['name' => 'Hilo Poliéster', 'code' => 'MAT-002']);

        $this->actingAs($this->admin)
            ->get('/raw-materials?search=Denim')
            ->assertOk()
            ->assertSee('Tela Denim')
            ->assertDontSee('Hilo Poliéster');
    }

    public function test_admin_can_create_raw_material(): void
    {
        $this->actingAs($this->admin)->post('/raw-materials', [
            'code' => 'MAT-100',
            'name' => 'Tela Nueva',
            'category' => 'Telas',
            'unit' => 'meter',
            'stock_qty' => 100,
            'min_stock' => 10,
            'cost' => 15000,
            'is_active' => 1,
        ])->assertRedirect('/raw-materials');

        $this->assertDatabaseHas('raw_materials', ['code' => 'MAT-100']);
    }

    public function test_validation_rejects_duplicate_code(): void
    {
        RawMaterial::factory()->create(['code' => 'MAT-100']);

        $this->actingAs($this->admin)
            ->post('/raw-materials', [
                'code' => 'MAT-100',
                'name' => 'Otra Materia',
                'unit' => 'unit',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_validation_rejects_invalid_unit(): void
    {
        $this->actingAs($this->admin)
            ->post('/raw-materials', [
                'code' => 'MAT-200',
                'name' => 'Materia Inválida',
                'unit' => 'litro',
            ])
            ->assertSessionHasErrors('unit');
    }

    public function test_admin_can_update_raw_material(): void
    {
        $material = RawMaterial::factory()->create(['name' => 'Nombre Antiguo']);

        $this->actingAs($this->admin)
            ->put("/raw-materials/{$material->id}", [
                'code' => $material->code,
                'name' => 'Nombre Nuevo',
                'unit' => $material->unit,
                'is_active' => 1,
            ])
            ->assertRedirect('/raw-materials');

        $this->assertDatabaseHas('raw_materials', ['id' => $material->id, 'name' => 'Nombre Nuevo']);
    }

    public function test_admin_can_delete_raw_material(): void
    {
        $material = RawMaterial::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/raw-materials/{$material->id}")
            ->assertRedirect('/raw-materials');

        $this->assertDatabaseMissing('raw_materials', ['id' => $material->id]);
    }
}