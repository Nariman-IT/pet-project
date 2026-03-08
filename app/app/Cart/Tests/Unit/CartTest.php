<?php

declare(strict_types=1);

namespace App\Cart\Tests\Unit;

use App\Models\User;
use App\Product\Models\Product;
use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class CartTest extends TestCase
{
    use RefreshDatabase;

    protected array $connectionsToTransact = ['pgsql'];

    public function test_user_can_view_empty_cart(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('api/v1/cart');

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'cart' => [
                    'id',
                    'user_id',
                    'items',
                    'total_price',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }


    public function test_user_can_add_item_to_cart_with_valid_quantity(): void
    {   
        /** @var Product $product */
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/cart', [
            'product_id' => $product->id,
            'quantity' => fake()->numberBetween(int1: 1, int2: 10),
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'cart' => [
                    'id',
                    'user_id',
                    'items',
                    'total_price',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_cannot_add_item_to_cart_with_excessive_quantity(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/cart', [
            'product_id' => $product->id,
            'quantity' => fake()->numberBetween(int1: 100, int2: 150),
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(structure: [
                'message',
                'errors',
            ]);
    }

    public function test_user_can_update_cart_item_quantity_with_valid_value(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $user = User::factory()->create();
        /** @var Cart $cart */
        $cart = Cart::factory()->for($user)->create();
        /** @var CartItem $cartItem */
        $cartItem = CartItem::factory()->for($cart)->for($product)->create();
        $response = $this->actingAs($user)->patchJson('/api/v1/cart/' . $cartItem->id, [
            'quantity' => fake()->numberBetween(int1: 1, int2: 7),
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'cart' => [
                    'id',
                    'user_id',
                    'items',
                    'total_price',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }


    public function test_user_cannot_update_cart_item_with_excessive_quantity(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $user = User::factory()->create();
        /** @var Cart $cart */
        $cart = Cart::factory()->for($user)->create();
        /** @var CartItem $cartItem */
        $cartItem = CartItem::factory()->for($cart)->for($product)->create();
        $response = $this->actingAs($user)->patchJson('/api/v1/cart/' . $cartItem->id, [
            'quantity' => fake()->numberBetween(int1: 20, int2: 25),
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(structure: [
                'message',
                'errors',
            ]);
    }


    public function test_user_can_remove_specific_item_from_cart(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $user = User::factory()->create();
        /** @var Cart $cart */
        $cart = Cart::factory()->for($user)->create();
        /** @var CartItem $cartItem */
        $cartItem = CartItem::factory()->for($cart)->for($product)->create();
        $response = $this->actingAs($user)->deleteJson('/api/v1/cart/' . $cartItem->id);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'id'
            ]);
    }


    public function test_user_can_clear_entire_cart(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $user = User::factory()->create();
        /** @var Cart $cart */
        $cart = Cart::factory()->for($user)->create();
        /** @var CartItem $cartItem */
        $cartItem = CartItem::factory()->for($cart)->for($product)->create();
        $response = $this->actingAs($user)->deleteJson('/api/v1/cart');

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'cart' => [
                    'id',
                    'user_id',
                    'items',
                    'total_price',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }
}
