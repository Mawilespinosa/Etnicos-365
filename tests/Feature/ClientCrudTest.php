<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCrudTest extends TestCase
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
        $this->get('/clients')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/clients')->assertForbidden();
    }

    public function test_admin_can_list_clients(): void
    {
        Client::factory()->create(['name' => 'Cliente Demo']);

        $this->actingAs($this->admin)
            ->get('/clients')
            ->assertOk()
            ->assertSee('Cliente Demo');
    }

    public function test_admin_can_search_clients(): void
    {
        Client::factory()->create(['name' => 'Almacén Étnico', 'document_number' => '900123456']);
        Client::factory()->create(['name' => 'Tienda Jeans', 'document_number' => '901234567']);

        $this->actingAs($this->admin)
            ->get('/clients?search=Étnico')
            ->assertOk()
            ->assertSee('Almacén Étnico')
            ->assertDontSee('Tienda Jeans');
    }

    public function test_admin_can_create_client(): void
    {
        $this->actingAs($this->admin)->post('/clients', [
            'name' => 'Nuevo Cliente',
            'document_type' => 'CC',
            'document_number' => '9876543210',
            'phone' => '3209876543',
            'email' => 'cliente@example.com',
            'city' => 'Medellín',
            'is_active' => 1,
        ])->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', ['document_number' => '9876543210']);
    }

    public function test_validation_rejects_duplicate_document_number(): void
    {
        Client::factory()->create(['document_number' => '9876543210']);

        $this->actingAs($this->admin)
            ->post('/clients', [
                'name' => 'Otro Cliente',
                'document_type' => 'CC',
                'document_number' => '9876543210',
            ])
            ->assertSessionHasErrors('document_number');
    }

    public function test_admin_can_update_client(): void
    {
        $client = Client::factory()->create(['name' => 'Nombre Antiguo']);

        $this->actingAs($this->admin)
            ->put("/clients/{$client->id}", [
                'name' => 'Nombre Nuevo',
                'document_type' => $client->document_type,
                'document_number' => $client->document_number,
                'is_active' => 1,
            ])
            ->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Nombre Nuevo']);
    }

    public function test_admin_can_delete_client(): void
    {
        $client = Client::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/clients/{$client->id}")
            ->assertRedirect('/clients');

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}