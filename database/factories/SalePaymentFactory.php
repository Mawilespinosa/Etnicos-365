<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalePayment>
 */
class SalePaymentFactory extends Factory
{
    protected $model = SalePayment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'amount' => fake()->randomFloat(2, 1000, 1000000),
            'payment_date' => fake()->date(),
            'method' => fake()->randomElement(['cash', 'transfer', 'card', 'check']),
            'user_id' => null,
            'notes' => null,
        ];
    }
}