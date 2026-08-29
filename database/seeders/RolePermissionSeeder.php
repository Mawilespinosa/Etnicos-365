<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the role -> permissions matrix (see PLAN.md section 8).
     */
    public function run(): void
    {
        $matrix = [
            'admin' => Permission::pluck('name')->all(),
            'production' => [
                'suppliers.view',
                'products.view',
                'raw_materials.view',
                'bill_of_materials.view',
                'production.view',
                'production.create',
                'production.update',
                'production.delete',
                'production.advance',
                'dashboard.view',
            ],
            'inventory' => [
                'suppliers.view',
                'products.view',
                'products.update',
                'raw_materials.view',
                'raw_materials.update',
                'production.view',
                'inventory.view',
                'inventory.update',
                'inventory.movements',
                'inventory.delete',
                'dashboard.view',
            ],
            'sales' => [
                'sellers.view',
                'sellers.create',
                'sellers.update',
                'clients.view',
                'clients.create',
                'clients.update',
                'products.view',
                'inventory.view',
                'sales.view',
                'sales.create',
                'sales.update',
                'sales.delete',
                'dashboard.view',
            ],
            'finance' => [
                'products.view',
                'sales.view',
                'finances.view',
                'finances.create',
                'finances.update',
                'finances.delete',
                'reports.export',
                'dashboard.view',
            ],
        ];

        foreach ($matrix as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->permissions()->sync(
                    Permission::whereIn('name', $permissionNames)->pluck('id')
                );
            }
        }
    }
}