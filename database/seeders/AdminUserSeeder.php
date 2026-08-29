<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the initial administrator user.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@etnicos365.com'],
            [
                'name' => 'Administrador',
                // Demo default for local dev; override with ADMIN_PASSWORD in .env for any other environment.
                'password' => env('ADMIN_PASSWORD') ?: 'Admin123!',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}