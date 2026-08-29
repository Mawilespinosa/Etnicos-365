<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('PRO-[0-9]{3}'),
            'name' => fake()->randomElement(['Jean Clásico', 'Jean Slim', 'Jean Recto']),
            'description' => fake()->sentence(),
            'size' => fake()->randomElement(['28', '30', '32', '34', '36']),
            'color' => fake()->randomElement(['Azul', 'Negro', 'Gris']),
            'model' => fake()->randomElement(['Clásico', 'Slim', 'Recto']),
            'category' => fake()->randomElement(['Hombre', 'Mujer', 'Unisex']),
            'cost' => fake()->randomFloat(2, 30000, 80000),
            'price' => fake()->randomFloat(2, 60000, 150000),
            'stock_qty' => fake()->randomFloat(0, 0, 500),
            'min_stock' => fake()->randomFloat(0, 10, 50),
            'is_active' => true,
        ];
    }
}