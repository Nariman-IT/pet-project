<?php

namespace App\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Product\Models\Product;


class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(10, 1000),
            'weight' => $this->faker->numberBetween(1, 100),
            'category' => $this->faker->randomElement(['pizza', 'drink']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
