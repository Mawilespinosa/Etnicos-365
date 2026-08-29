<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the application's permissions (module.action).
     */
    public function run(): void
    {
        $permissions = [
            // Users
            ['name' => 'users.view', 'module' => 'users', 'display_name' => 'Ver usuarios'],
            ['name' => 'users.create', 'module' => 'users', 'display_name' => 'Crear usuarios'],
            ['name' => 'users.update', 'module' => 'users', 'display_name' => 'Editar usuarios'],
            ['name' => 'users.delete', 'module' => 'users', 'display_name' => 'Eliminar usuarios'],
            // Roles
            ['name' => 'roles.view', 'module' => 'roles', 'display_name' => 'Ver roles'],
            ['name' => 'roles.create', 'module' => 'roles', 'display_name' => 'Crear roles'],
            ['name' => 'roles.update', 'module' => 'roles', 'display_name' => 'Editar roles'],
            ['name' => 'roles.delete', 'module' => 'roles', 'display_name' => 'Eliminar roles'],
            // Sellers
            ['name' => 'sellers.view', 'module' => 'sellers', 'display_name' => 'Ver vendedores'],
            ['name' => 'sellers.create', 'module' => 'sellers', 'display_name' => 'Crear vendedores'],
            ['name' => 'sellers.update', 'module' => 'sellers', 'display_name' => 'Editar vendedores'],
            ['name' => 'sellers.delete', 'module' => 'sellers', 'display_name' => 'Eliminar vendedores'],
            // Clients
            ['name' => 'clients.view', 'module' => 'clients', 'display_name' => 'Ver clientes'],
            ['name' => 'clients.create', 'module' => 'clients', 'display_name' => 'Crear clientes'],
            ['name' => 'clients.update', 'module' => 'clients', 'display_name' => 'Editar clientes'],
            ['name' => 'clients.delete', 'module' => 'clients', 'display_name' => 'Eliminar clientes'],
            // Suppliers
            ['name' => 'suppliers.view', 'module' => 'suppliers', 'display_name' => 'Ver proveedores'],
            ['name' => 'suppliers.create', 'module' => 'suppliers', 'display_name' => 'Crear proveedores'],
            ['name' => 'suppliers.update', 'module' => 'suppliers', 'display_name' => 'Editar proveedores'],
            ['name' => 'suppliers.delete', 'module' => 'suppliers', 'display_name' => 'Eliminar proveedores'],
            // Products
            ['name' => 'products.view', 'module' => 'products', 'display_name' => 'Ver productos'],
            ['name' => 'products.create', 'module' => 'products', 'display_name' => 'Crear productos'],
            ['name' => 'products.update', 'module' => 'products', 'display_name' => 'Editar productos'],
            ['name' => 'products.delete', 'module' => 'products', 'display_name' => 'Eliminar productos'],
            // Raw materials
            ['name' => 'raw_materials.view', 'module' => 'raw_materials', 'display_name' => 'Ver materias primas'],
            ['name' => 'raw_materials.create', 'module' => 'raw_materials', 'display_name' => 'Crear materias primas'],
            ['name' => 'raw_materials.update', 'module' => 'raw_materials', 'display_name' => 'Editar materias primas'],
            ['name' => 'raw_materials.delete', 'module' => 'raw_materials', 'display_name' => 'Eliminar materias primas'],
            // Bill of materials
            ['name' => 'bill_of_materials.view', 'module' => 'bill_of_materials', 'display_name' => 'Ver lista de materiales'],
            ['name' => 'bill_of_materials.create', 'module' => 'bill_of_materials', 'display_name' => 'Crear lista de materiales'],
            ['name' => 'bill_of_materials.update', 'module' => 'bill_of_materials', 'display_name' => 'Editar lista de materiales'],
            ['name' => 'bill_of_materials.delete', 'module' => 'bill_of_materials', 'display_name' => 'Eliminar lista de materiales'],
            // Production
            ['name' => 'production.view', 'module' => 'production', 'display_name' => 'Ver órdenes de producción'],
            ['name' => 'production.create', 'module' => 'production', 'display_name' => 'Crear órdenes de producción'],
            ['name' => 'production.update', 'module' => 'production', 'display_name' => 'Editar órdenes de producción'],
            ['name' => 'production.delete', 'module' => 'production', 'display_name' => 'Eliminar órdenes de producción'],
            ['name' => 'production.advance', 'module' => 'production', 'display_name' => 'Avanzar etapas de producción'],
            // Inventory
            ['name' => 'inventory.view', 'module' => 'inventory', 'display_name' => 'Ver inventario'],
            ['name' => 'inventory.update', 'module' => 'inventory', 'display_name' => 'Editar inventario'],
            ['name' => 'inventory.movements', 'module' => 'inventory', 'display_name' => 'Registrar movimientos de inventario'],
            ['name' => 'inventory.delete', 'module' => 'inventory', 'display_name' => 'Eliminar inventario'],
            // Sales
            ['name' => 'sales.view', 'module' => 'sales', 'display_name' => 'Ver ventas'],
            ['name' => 'sales.create', 'module' => 'sales', 'display_name' => 'Crear ventas'],
            ['name' => 'sales.update', 'module' => 'sales', 'display_name' => 'Editar ventas'],
            ['name' => 'sales.delete', 'module' => 'sales', 'display_name' => 'Eliminar ventas'],
            // Finances
            ['name' => 'finances.view', 'module' => 'finances', 'display_name' => 'Ver finanzas'],
            ['name' => 'finances.create', 'module' => 'finances', 'display_name' => 'Registrar ingresos y egresos'],
            ['name' => 'finances.update', 'module' => 'finances', 'display_name' => 'Editar finanzas'],
            ['name' => 'finances.delete', 'module' => 'finances', 'display_name' => 'Eliminar finanzas'],
            // Reports
            ['name' => 'reports.export', 'module' => 'reports', 'display_name' => 'Exportar reportes'],
            // Dashboard
            ['name' => 'dashboard.view', 'module' => 'dashboard', 'display_name' => 'Ver dashboard'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }
    }
}