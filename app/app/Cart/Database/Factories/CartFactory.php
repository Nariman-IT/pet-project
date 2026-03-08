<?php

declare(strict_types=1);

namespace App\Cart\Database\Factories;

use App\Models\User;
use App\Cart\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;


final class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
