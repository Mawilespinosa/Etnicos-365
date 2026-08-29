<?php

namespace Database\Factories;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RawMaterial>
 */
class RawMaterialFactory extends Factory
{
    protected $model = RawMaterial::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('MAT-[0-9]{3}'),
            'name' => fake()->randomElement(['Tela Denim', 'Hilo de poliéster', 'Botones metálicos', 'Cierres', 'Etiquetas', 'Tintura índigo']),
            'category' => fake()->randomElement(['Telas', 'Insumos', 'Químicos']),
            'unit' => fake()->randomElement(['unit', 'meter', 'kg', 'roll']),
            'stock_qty' => fake()->randomFloat(2, 0, 1000),
            'min_stock' => fake()->randomFloat(2, 0, 100),
            'cost' => fake()->randomFloat(2, 1000, 50000),
            'is_active' => true,
        ];
    }
}