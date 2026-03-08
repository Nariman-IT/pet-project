<?php

declare(strict_types=1);

namespace App\Cart\Database\Factories;

use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


final class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(int1: 1, int2: 3),
        ];
    }
}
