<?php

namespace Database\Factories;

use App\Models\BillOfMaterial;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillOfMaterial>
 */
class BillOfMaterialFactory extends Factory
{
    protected $model = BillOfMaterial::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'raw_material_id' => RawMaterial::factory(),
            'quantity' => fake()->randomFloat(2, 0.1, 10),
            'unit' => fake()->randomElement(['unit', 'meter', 'kg', 'roll']),
            'notes' => null,
        ];
    }
}