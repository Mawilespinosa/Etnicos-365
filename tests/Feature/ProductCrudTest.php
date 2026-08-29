<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
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
        $this->get('/products')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/products')->assertForbidden();
    }

    public function test_admin_can_list_products(): void
    {
        Product::factory()->create(['name' => 'Jean Demo']);

        $this->actingAs($this->admin)
            ->get('/products')
            ->assertOk()
            ->assertSee('Jean Demo');
    }

    public function test_admin_can_search_products(): void
    {
        Product::factory()->create(['name' => 'Jean Clásico', 'code' => 'PRO-001', 'model' => 'Clásico']);
        Product::factory()->create(['name' => 'Jean Slim', 'code' => 'PRO-002', 'model' => 'Slim']);

        $this->actingAs($this->admin)
            ->get('/products?search=Clásico')
            ->assertOk()
            ->assertSee('Jean Clásico')
            ->assertDontSee('Jean Slim');
    }

    public function test_admin_can_create_product(): void
    {
        $this->actingAs($this->admin)->post('/products', [
            'code' => 'PRO-100',
            'name' => 'Jean Nuevo',
            'description' => 'Descripción de prueba',
            'size' => '32',
            'color' => 'Azul',
            'model' => 'Clásico',
            'category' => 'Hombre',
            'cost' => 45000,
            'price' => 95000,
            'stock_qty' => 10,
            'min_stock' => 2,
            'is_active' => 1,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['code' => 'PRO-100']);
    }

    public function test_validation_rejects_duplicate_code(): void
    {
        Product::factory()->create(['code' => 'PRO-100']);

        $this->actingAs($this->admin)
            ->post('/products', [
                'code' => 'PRO-100',
                'name' => 'Otro Producto',
                'cost' => 1000,
                'price' => 2000,
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Nombre Antiguo']);

        $this->actingAs($this->admin)
            ->put("/products/{$product->id}", [
                'code' => $product->code,
                'name' => 'Nombre Nuevo',
                'cost' => $product->cost,
                'price' => $product->price,
                'is_active' => 1,
            ])
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nombre Nuevo']);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_create_product_with_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('product.jpg', 100, 100);

        $this->actingAs($this->admin)->post('/products', [
            'code' => 'PRO-IMG-001',
            'name' => 'Jean Con Imagen',
            'description' => 'Producto con imagen de prueba',
            'size' => '32',
            'color' => 'Azul',
            'model' => 'Clásico',
            'category' => 'Hombre',
            'cost' => 45000,
            'price' => 95000,
            'stock_qty' => 10,
            'min_stock' => 2,
            'is_active' => 1,
            'image' => $file,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['code' => 'PRO-IMG-001']);

        $product = Product::where('code', 'PRO-IMG-001')->first();
        $this->assertNotNull($product->image);
        $this->assertStringStartsWith('products/', $product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_update_product_with_new_image_deletes_old(): void
    {
        Storage::fake('public');

        // Create product with initial image
        $oldFile = UploadedFile::fake()->image('old.jpg', 100, 100);
        $product = Product::factory()->create([
            'name' => 'Producto Original',
            'image' => 'products/old-image.jpg',
        ]);

        // Manually create the old file in storage for the test
        Storage::disk('public')->put('products/old-image.jpg', 'old content');

        $newFile = UploadedFile::fake()->image('new.jpg', 100, 100);

        $this->actingAs($this->admin)
            ->put("/products/{$product->id}", [
                'code' => $product->code,
                'name' => 'Producto Actualizado',
                'cost' => $product->cost,
                'price' => $product->price,
                'is_active' => 1,
                'image' => $newFile,
            ])
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Producto Actualizado']);

        $product->refresh();
        $this->assertNotNull($product->image);
        $this->assertStringStartsWith('products/', $product->image);
        $this->assertNotEquals('products/old-image.jpg', $product->image);
        Storage::disk('public')->assertExists($product->image);
        Storage::disk('public')->assertMissing('products/old-image.jpg');
    }

    public function test_destroy_product_deletes_image_from_storage(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'image' => 'products/test-image.jpg',
        ]);

        // Create the file in storage
        Storage::disk('public')->put('products/test-image.jpg', 'image content');
        Storage::disk('public')->assertExists('products/test-image.jpg');

        $this->actingAs($this->admin)
            ->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/test-image.jpg');
    }
}