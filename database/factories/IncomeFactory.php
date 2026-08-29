<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
class IncomeFactory extends Factory
{
    protected $model = Income::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['sale', 'other']),
            'reference_type' => null,
            'reference_id' => null,
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 10000, 5000000),
            'income_date' => fake()->date(),
            'user_id' => User::factory(),
        ];
    }
}