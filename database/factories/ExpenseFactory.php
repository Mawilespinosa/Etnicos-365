<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['raw_material', 'labor', 'services', 'other']),
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 10000, 5000000),
            'expense_date' => fake()->date(),
            'user_id' => User::factory(),
        ];
    }
}