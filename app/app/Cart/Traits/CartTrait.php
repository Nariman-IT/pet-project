<?php

namespace App\Cart\Traits;

use App\Cart\Models\Cart;
use App\Product\Models\Product;
use Illuminate\Validation\ValidationException;

trait CartTrait
{
    protected function validateCartRules(Cart $cart, Product $product, $quantity): void
    {
        if($product->category === Product::CATEGORY_PIZZA) {
            $count = $cart->pizzaCount() + $quantity;
            if ($count > 10) {
                throw ValidationException::withMessages([
                    'quantity' => 'Max 10 pizzas per basket'
                ]);
            }
        }

        if($product->category === Product::CATEGORY_DRINK) {
            $count = $cart->drinkCount() + $quantity;
            if ($count > 20) {
                throw ValidationException::withMessages([
                    'quantity' => 'Max 20 drinks per basket'
                ]);
            }
        }
    }
}