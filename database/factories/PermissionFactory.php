<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = fake()->randomElement(['users', 'roles', 'sales', 'products', 'inventory']);
        $action = fake()->randomElement(['view', 'create', 'update', 'delete']);

        return [
            'name' => $module.'.'.$action,
            'module' => $module,
            'display_name' => ucfirst($action).' '.$module,
            'description' => fake()->sentence(),
        ];
    }
}