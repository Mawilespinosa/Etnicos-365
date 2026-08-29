<?php

namespace Database\Factories;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductionOrderStage>
 */
class ProductionOrderStageFactory extends Factory
{
    protected $model = ProductionOrderStage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'production_order_id' => ProductionOrder::factory(),
            'stage_number' => fake()->numberBetween(1, 8),
            'name' => fake()->randomElement([
                'Compra de tela',
                'Corte',
                'Confección',
                'Pulido',
                'Lavandería',
                'Empaque',
                'Bodega',
                'Distribución',
            ]),
            'status' => 'pending',
            'notes' => null,
            'completed_by' => null,
            'completed_at' => null,
        ];
    }
}