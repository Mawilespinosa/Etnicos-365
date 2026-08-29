<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_user_without_dashboard_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::where('email', 'admin@etnicos365.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/users')
            ->assertOk()
            ->assertSee('Usuarios');
    }

    public function test_admin_can_create_user_and_assign_role(): void
    {
        $admin = User::where('email', 'admin@etnicos365.com')->firstOrFail();
        $role = Role::where('name', 'sales')->firstOrFail();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Vendedora Demo',
            'email' => 'vendedora@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_active' => 1,
            'roles' => [$role->id],
        ])->assertRedirect('/users');

        $this->assertDatabaseHas('users', ['email' => 'vendedora@example.com']);

        $created = User::where('email', 'vendedora@example.com')->firstOrFail();
        $this->assertTrue($created->hasRole('sales'));
    }

    public function test_admin_can_create_role_with_permissions(): void
    {
        $admin = User::where('email', 'admin@etnicos365.com')->firstOrFail();
        $permission = Permission::where('name', 'sales.view')->firstOrFail();

        $this->actingAs($admin)->post('/roles', [
            'name' => 'cajero',
            'display_name' => 'Cajero',
            'permissions' => [$permission->id],
        ])->assertRedirect('/roles');

        $role = Role::where('name', 'cajero')->firstOrFail();
        $this->assertTrue($role->permissions()->where('name', 'sales.view')->exists());
    }

    public function test_validation_rejects_duplicate_role_name(): void
    {
        $admin = User::where('email', 'admin@etnicos365.com')->firstOrFail();

        $this->actingAs($admin)
            ->post('/roles', [
                'name' => 'admin',
                'display_name' => 'Administrador',
            ])
            ->assertSessionHasErrors('name');
    }
}