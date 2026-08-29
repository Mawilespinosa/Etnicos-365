<?php

namespace Database\Seeders;

use App\Models\BillOfMaterial;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Seller;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed demo catalogs in Spanish.
     */
    public function run(): void
    {
        $this->seedClients();
        $this->seedSellers();
        $this->seedSuppliers();
        $this->seedRawMaterials();
        $this->seedProducts();
        $this->seedBillOfMaterials();
        $this->seedInventory();
    }

    private function seedClients(): void
    {
        $clients = [
            ['name' => 'Almacén Étnico', 'document_type' => 'NIT', 'document_number' => '900123456', 'phone' => '6015550101', 'email' => 'contacto@almacenetico.co', 'address' => 'Cra 15 # 88-12', 'city' => 'Bogotá'],
            ['name' => 'Tienda Jeans Center', 'document_type' => 'NIT', 'document_number' => '901234567', 'phone' => '6044445566', 'email' => 'ventas@jeanscenter.co', 'address' => 'Cl 45 # 22-30', 'city' => 'Medellín'],
            ['name' => 'Moda Urbana SAS', 'document_type' => 'NIT', 'document_number' => '902345678', 'phone' => '6023334455', 'email' => 'compras@modaurbana.co', 'address' => 'Av 5N # 12-45', 'city' => 'Cali'],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(['document_number' => $client['document_number']], $client);
        }
    }

    private function seedSellers(): void
    {
        $sellers = [
            ['name' => 'Carlos Pérez', 'document_type' => 'CC', 'document_number' => '1000000001', 'phone' => '3101112233', 'email' => 'carlos.perez@etnicos365.com', 'address' => 'Cl 10 # 5-20', 'city' => 'Bogotá', 'commission_rate' => 5],
            ['name' => 'Luisa Gómez', 'document_type' => 'CC', 'document_number' => '1000000002', 'phone' => '3202223344', 'email' => 'luisa.gomez@etnicos365.com', 'address' => 'Cra 20 # 34-56', 'city' => 'Medellín', 'commission_rate' => 4],
        ];

        foreach ($sellers as $seller) {
            Seller::updateOrCreate(['document_number' => $seller['document_number']], $seller);
        }
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            ['name' => 'Textiles del Valle', 'document_type' => 'NIT', 'document_number' => '890100001', 'phone' => '6025557788', 'email' => 'ventas@textilesdelvalle.co', 'address' => 'Cra 8 # 15-90', 'city' => 'Cali', 'contact_name' => 'Marta Ríos'],
            ['name' => 'Insumos Jeans SAS', 'document_type' => 'NIT', 'document_number' => '890200002', 'phone' => '6013334455', 'email' => 'pedidos@insumosjeans.co', 'address' => 'Av Boyacá # 45-10', 'city' => 'Bogotá', 'contact_name' => 'Jorge Salazar'],
            ['name' => 'Proveeduría Industrial', 'document_type' => 'NIT', 'document_number' => '890300003', 'phone' => '6042223344', 'email' => 'compras@provindustrial.co', 'address' => 'Cl 30 # 50-25', 'city' => 'Medellín', 'contact_name' => 'Ana Torres'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['document_number' => $supplier['document_number']], $supplier);
        }
    }

    private function seedRawMaterials(): void
    {
        $materials = [
            ['code' => 'MAT-001', 'name' => 'Tela Denim Azul', 'category' => 'Telas', 'unit' => 'meter', 'stock_qty' => 500, 'min_stock' => 100, 'cost' => 18000],
            ['code' => 'MAT-002', 'name' => 'Tela Denim Negro', 'category' => 'Telas', 'unit' => 'meter', 'stock_qty' => 400, 'min_stock' => 80, 'cost' => 19000],
            ['code' => 'MAT-003', 'name' => 'Hilo de Poliéster', 'category' => 'Insumos', 'unit' => 'roll', 'stock_qty' => 120, 'min_stock' => 30, 'cost' => 25000],
            ['code' => 'MAT-004', 'name' => 'Botones Metálicos', 'category' => 'Insumos', 'unit' => 'unit', 'stock_qty' => 3000, 'min_stock' => 500, 'cost' => 150],
            ['code' => 'MAT-005', 'name' => 'Cierres Metálicos', 'category' => 'Insumos', 'unit' => 'unit', 'stock_qty' => 1500, 'min_stock' => 300, 'cost' => 800],
            ['code' => 'MAT-006', 'name' => 'Etiquetas', 'category' => 'Insumos', 'unit' => 'unit', 'stock_qty' => 2500, 'min_stock' => 500, 'cost' => 120],
            ['code' => 'MAT-007', 'name' => 'Tintura Índigo', 'category' => 'Químicos', 'unit' => 'kg', 'stock_qty' => 80, 'min_stock' => 20, 'cost' => 32000],
        ];

        foreach ($materials as $material) {
            RawMaterial::updateOrCreate(['code' => $material['code']], $material);
        }
    }

    private function seedProducts(): void
    {
        $products = [
            ['code' => 'PRO-001', 'name' => 'Jean Clásico Hombre', 'description' => 'Jean clásico de corte recto, tiro medio.', 'size' => '32', 'color' => 'Azul', 'model' => 'Clásico', 'category' => 'Hombre', 'cost' => 45000, 'price' => 95000, 'stock_qty' => 120, 'min_stock' => 20],
            ['code' => 'PRO-002', 'name' => 'Jean Slim Mujer', 'description' => 'Jean slim de talle alto, elastizado.', 'size' => '28', 'color' => 'Azul', 'model' => 'Slim', 'category' => 'Mujer', 'cost' => 48000, 'price' => 99000, 'stock_qty' => 100, 'min_stock' => 15],
            ['code' => 'PRO-003', 'name' => 'Jean Recto Unisex', 'description' => 'Jean recto de corte amplio, color negro.', 'size' => '30', 'color' => 'Negro', 'model' => 'Recto', 'category' => 'Unisex', 'cost' => 47000, 'price' => 97000, 'stock_qty' => 90, 'min_stock' => 15],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['code' => $product['code']], $product);
        }
    }

    private function seedBillOfMaterials(): void
    {
        $bom = [
            ['product_code' => 'PRO-001', 'raw_material_code' => 'MAT-001', 'quantity' => 1.5, 'unit' => 'meter', 'notes' => 'Tela principal'],
            ['product_code' => 'PRO-001', 'raw_material_code' => 'MAT-003', 'quantity' => 1, 'unit' => 'roll', 'notes' => null],
            ['product_code' => 'PRO-001', 'raw_material_code' => 'MAT-004', 'quantity' => 5, 'unit' => 'unit', 'notes' => null],
            ['product_code' => 'PRO-001', 'raw_material_code' => 'MAT-005', 'quantity' => 1, 'unit' => 'unit', 'notes' => null],
            ['product_code' => 'PRO-001', 'raw_material_code' => 'MAT-006', 'quantity' => 1, 'unit' => 'unit', 'notes' => null],
            ['product_code' => 'PRO-002', 'raw_material_code' => 'MAT-001', 'quantity' => 1.2, 'unit' => 'meter', 'notes' => 'Tela principal'],
            ['product_code' => 'PRO-002', 'raw_material_code' => 'MAT-003', 'quantity' => 1, 'unit' => 'roll', 'notes' => null],
            ['product_code' => 'PRO-002', 'raw_material_code' => 'MAT-004', 'quantity' => 5, 'unit' => 'unit', 'notes' => null],
            ['product_code' => 'PRO-002', 'raw_material_code' => 'MAT-005', 'quantity' => 1, 'unit' => 'unit', 'notes' => null],
            ['product_code' => 'PRO-003', 'raw_material_code' => 'MAT-002', 'quantity' => 1.5, 'unit' => 'meter', 'notes' => 'Tela principal'],
            ['product_code' => 'PRO-003', 'raw_material_code' => 'MAT-007', 'quantity' => 0.2, 'unit' => 'kg', 'notes' => 'Tintura para acabado'],
            ['product_code' => 'PRO-003', 'raw_material_code' => 'MAT-006', 'quantity' => 1, 'unit' => 'unit', 'notes' => null],
        ];

        foreach ($bom as $item) {
            $product = Product::where('code', $item['product_code'])->first();
            $rawMaterial = RawMaterial::where('code', $item['raw_material_code'])->first();

            if ($product && $rawMaterial) {
                BillOfMaterial::updateOrCreate(
                    ['product_id' => $product->id, 'raw_material_id' => $rawMaterial->id],
                    [
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'notes' => $item['notes'],
                    ]
                );
            }
        }
    }

    private function seedInventory(): void
    {
        foreach (Product::all() as $product) {
            Inventory::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'location' => 'Bodega principal',
                    'stock_qty' => $product->stock_qty,
                    'min_stock' => $product->min_stock,
                ]
            );
        }
    }
}