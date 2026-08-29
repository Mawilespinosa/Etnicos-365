<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'location' => fake()->randomElement(['Bodega principal', 'Bodega secundaria']),
            'stock_qty' => fake()->randomFloat(0, 0, 500),
            'min_stock' => fake()->randomFloat(0, 5, 50),
        ];
    }
}