<?php

declare(strict_types=1);

namespace App\Order\Tests\Unit;

use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Order\Models\Order;
use App\Product\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_order_with_valid_cart_and_address(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        $product = Product::factory()->create();
        CartItem::factory()->for(factory: $cart)->for(factory: $product)->create(attributes: ['quantity' => 2]);

        $data = [
            'delivery_address_region' => fake()->text(50),
            'delivery_address_city' => fake()->text(50),
            'delivery_address_street' => fake()->text(50),
            'delivery_address_house' => fake()->text(50),
            'delivery_address_entrance' => fake()->text(50),
            'delivery_address_apartment' => fake()->text(50),
        ];


        $response = $this->actingAs($user)->postJson('api/v1/orders', $data);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_CREATED)
            ->assertJsonStructure(structure: [
                'order' => [
                    'id',
                    'user_id',
                    'status',
                    'delivery_address',
                    'full_price',
                    'items',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_cannot_create_order_without_cart(): void
    {
        $user = User::factory()->create();

        $data = [
            'delivery_address_region' => fake()->text(50),
            'delivery_address_city' => fake()->text(50),
            'delivery_address_street' => fake()->text(50),
            'delivery_address_house' => fake()->text(50),
            'delivery_address_entrance' => fake()->text(50),
            'delivery_address_apartment' => fake()->text(50),
        ];

        $response = $this->actingAs($user)->postJson('api/v1/orders', $data);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(structure: [
                'message'
            ]);
    }

    public function test_order_creation_fails_with_invalid_address_data(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for(factory: $user)->create();
        $product = Product::factory()->create();
        CartItem::factory()->for(factory: $cart)->for(factory: $product)->create();

        $data = [
            'delivery_address_region' => '',
            'delivery_address_city' => '',
            'delivery_address_street' => fake()->text(50),
            'delivery_address_house' => fake()->text(50),
            'delivery_address_entrance' => fake()->text(50),
            'delivery_address_apartment' => fake()->text(50),
        ];

        $response = $this->actingAs($user)->postJson('api/v1/orders', $data);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(structure: [
                'message',
                'errors',
            ]);
    }

    public function test_user_can_view_all_their_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->for(factory: $user)->count(count: 3)->create();

        $response = $this->actingAs($user)->getJson('api/v1/orders');
        
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'orders'
            ]);
    }

    public function test_user_can_view_single_order_by_id(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for(factory: $user)->create();

        $response = $this->actingAs($user)->getJson('api/v1/orders/' . $order->id);
        
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'order' => [
                    'id',
                    'user_id',
                    'status',
                    'delivery_address',
                    'full_price',
                    'items',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_viewing_non_existent_order_returns_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('api/v1/orders/' . 10000);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(structure: [
                'message',
            ]);
    }



    public function test_user_can_update_order_status_to_paid(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for(factory: $user)->create();

        $data = [
            'status' => Order::PAID,
        ];

        $response = $this->actingAs($user)->patchJson('api/v1/orders/' . $order->id, $data);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'order' => [
                    'id',
                    'user_id',
                    'status',
                    'delivery_address',
                    'full_price',
                    'items',
                    'created_at',
                    'updated_at',
                ],
            ]);


    }


    public function test_order_status_update_fails_with_invalid_status(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for(factory: $user)->create();

        $data = [
            'status' => 'invalid_status',
        ];

        $response = $this->actingAs($user)->patchJson('api/v1/orders/' . $order->id, $data);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(structure: [
                'message'
            ]);
    }
}

