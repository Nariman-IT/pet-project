<?php

declare(strict_types=1);

namespace App\Order\Database\Factories;

use App\Order\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
final class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => Order::CREATED,
            'delivery_address' => [
                'delivery_address_region' => fake()->text(50),
                'delivery_address_city' => fake()->text(50),
                'delivery_address_street' => fake()->text(50),
                'delivery_address_house' => fake()->text(50),
                'delivery_address_entrance' => fake()->text(50),
                'delivery_address_apartment' => fake()->text(50),
            ],
            'full_price' => fake()->randomFloat(nbMaxDecimals: 2, min: 10, max: 1_000),
        ];
    }
}
