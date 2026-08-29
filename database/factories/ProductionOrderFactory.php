<?php

namespace Database\Factories;

use App\Models\ProductionOrder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductionOrder>
 */
class ProductionOrderFactory extends Factory
{
    protected $model = ProductionOrder::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('OT-[0-9]{4}'),
            'product_id' => Product::factory(),
            'quantity' => fake()->randomFloat(0, 10, 500),
            'current_stage' => 1,
            'status' => 'pending',
            'notes' => null,
            'created_by' => User::factory(),
            'started_at' => null,
            'completed_at' => null,
        ];
    }
}