<?php

namespace App\Product\Tests\Unit;

use Tests\TestCase;
use App\Product\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;


class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $connectionsToTransact = ['pgsql'];

    public function test_it_can_get_all_products(): void
    {
        $response = $this->getJson('api/v1/products');
        
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'products'
            ]);
    }

    
    public function test_it_returns_404_when_product_not_found(): void
    {
        $response = $this->getJson('api/v1/products/10000');

        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message',
            ]);
    }

 
    public function test_it_can_get_single_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson('api/v1/products/'. $product->id);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'product' => [ 
                                'id',
                                'name',
                                'description',
                                'price',
                                'weight',
                                'category',
                                'created_at',
                                'updated_at',
                            ],
            ]);
    }



    public function test_it_can_create_new_product(): void
    {
        $admin =  User::factory()->create([
            'role' => 'admin',
        ]);

        $product = [
            'name' => fake()->name,
            'description' => fake()->text(100),
            'price' => fake()->numberBetween(100, 1000),
            'weight' => fake()->numberBetween(100, 1000),
            'category' => 'pizza',
        ];

        $response = $this->actingAs($admin)->postJson('api/v1/products', $product);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'product' => [ 
                                'id',
                                'name',
                                'description',
                                'price',
                                'weight',
                                'category',
                                'created_at',
                                'updated_at',
                            ],
            ]);
    }



    public function test_regular_user_cannot_create_product(): void
    {
        $user =  User::factory()->create();

        $product = [
            'name' => fake()->name,
            'description' => fake()->text(100),
            'price' => fake()->numberBetween(100, 1000),
            'weight' => fake()->numberBetween(100, 1000),
            'category' => 'pizza',
        ];

        $response = $this->actingAs($user)->postJson('api/v1/products', $product);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonStructure([
                'message'
            ]);
    }


    public function test_it_validates_required_fields_when_creating_product(): void
    {
        $admin =  User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->postJson('api/v1/products', ['name' => '']);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }



    public function test_it_can_delete_existing_product(): void
    {
        $product = Product::factory()->create();
        $admin =  User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->deleteJson('api/v1/products/'. $product->id);

        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
            ]);
    }



    public function test_it_returns_404_when_deleting_nonexistent_product(): void
    {
        $admin =  User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->deleteJson('api/v1/products/1000');

        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message',
            ]);
    }



    public function test_it_can_update_existing_product(): void
    {
        $product = Product::factory()->create();
        $admin =  User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->patchJson('api/v1/products/'. $product->id, ['name' => 'nameTest']);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'product'
            ]);
    }


    public function test_regular_user_cannot_update_product(): void
    {
        $product = Product::factory()->create();
        $user =  User::factory()->create();

        $response = $this->actingAs($user)->patchJson('api/v1/products/'. $product->id, ['name' => 'nameTest']);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonStructure([
                'message'
            ]);
    }


    public function test_it_returns_404_when_updating_nonexistent_product(): void
    {
        $admin =  User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->patchJson('api/v1/products/1000', ['name' => 'nameTest']);
    
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message',
            ]);
    }


    public function test_it_validates_invalid_category_when_updating_product(): void
    {
        $product = Product::factory()->create();
        $admin =  User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->patchJson('api/v1/products/'. $product->id, ['category' => 'testProduct']);
        
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }
}

