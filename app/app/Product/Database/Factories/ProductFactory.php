<?php

declare(strict_types=1);

namespace App\Product\Database\Factories;

use App\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(int1: 10, int2: 1000),
            'weight' => $this->faker->numberBetween(int1: 1, int2: 100),
            'category' => $this->faker->randomElement(['pizza', 'drink']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
