<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's roles.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Acceso total al sistema.',
            ],
            [
                'name' => 'production',
                'display_name' => 'Producción',
                'description' => 'Gestiona órdenes de trabajo y etapas de producción.',
            ],
            [
                'name' => 'inventory',
                'display_name' => 'Inventario / Bodega',
                'description' => 'Administra inventario, stock y bodega.',
            ],
            [
                'name' => 'sales',
                'display_name' => 'Ventas',
                'description' => 'Gestiona clientes, vendedores y ventas.',
            ],
            [
                'name' => 'finance',
                'display_name' => 'Finanzas',
                'description' => 'Administra ingresos, egresos y reportes.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}