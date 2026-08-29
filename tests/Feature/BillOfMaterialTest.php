<?php

namespace Tests\Feature;

use App\Models\BillOfMaterial;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillOfMaterialTest extends TestCase
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
        $product = Product::factory()->create();

        $this->post("/products/{$product->id}/materials")->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)
            ->post("/products/{$product->id}/materials")
            ->assertForbidden();
    }

    public function test_admin_can_add_material_to_product(): void
    {
        $product = Product::factory()->create();
        $material = RawMaterial::factory()->create();

        $this->actingAs($this->admin)->post("/products/{$product->id}/materials", [
            'raw_material_id' => $material->id,
            'quantity' => 2.5,
            'unit' => 'meter',
            'notes' => 'Tela principal',
        ])->assertRedirect("/products/{$product->id}");

        $this->assertDatabaseHas('bill_of_materials', [
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
            'quantity' => 2.5,
        ]);
    }

    public function test_duplicate_material_is_rejected(): void
    {
        $product = Product::factory()->create();
        $material = RawMaterial::factory()->create();

        BillOfMaterial::factory()->create([
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
        ]);

        $this->actingAs($this->admin)
            ->post("/products/{$product->id}/materials", [
                'raw_material_id' => $material->id,
                'quantity' => 1,
            ])
            ->assertSessionHasErrors('raw_material_id');

        $this->assertSame(1, BillOfMaterial::where('product_id', $product->id)->count());
    }

    public function test_quantity_must_be_positive(): void
    {
        $product = Product::factory()->create();
        $material = RawMaterial::factory()->create();

        $this->actingAs($this->admin)
            ->post("/products/{$product->id}/materials", [
                'raw_material_id' => $material->id,
                'quantity' => 0,
            ])
            ->assertSessionHasErrors('quantity');
    }

    public function test_admin_can_remove_material_from_product(): void
    {
        $product = Product::factory()->create();
        $material = RawMaterial::factory()->create();

        BillOfMaterial::factory()->create([
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
        ]);

        $this->actingAs($this->admin)
            ->delete("/products/{$product->id}/materials/{$material->id}")
            ->assertRedirect("/products/{$product->id}");

        $this->assertDatabaseMissing('bill_of_materials', [
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
        ]);
    }

    public function test_product_show_displays_bom(): void
    {
        $product = Product::factory()->create();
        $material = RawMaterial::factory()->create(['name' => 'Tela Denim Azul']);

        BillOfMaterial::factory()->create([
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
            'quantity' => 1.5,
        ]);

        $this->actingAs($this->admin)
            ->get("/products/{$product->id}")
            ->assertOk()
            ->assertSee('Tela Denim Azul')
            ->assertSee('Lista de materiales');
    }
}