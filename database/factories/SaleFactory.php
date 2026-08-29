<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => fake()->unique()->regexify('FAC-[0-9]{4}'),
            'client_id' => Client::factory(),
            'seller_id' => null,
            'sale_date' => fake()->date(),
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'total' => 0,
            'status' => 'draft',
            'payment_status' => 'pending',
            'notes' => null,
        ];
    }
}