<?php

declare(strict_types=1);

namespace App\Product\Tests\Unit;

use App\Models\User;
use App\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected array $connectionsToTransact = ['pgsql'];

    public function testItCanGetAllProducts(): void
    {
        $response = $this->getJson('api/v1/products');

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'products',
            ]);
    }

    public function testItReturns404WhenProductNotFound(): void
    {
        $response = $this->getJson('api/v1/products/10000');

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(structure: [
                'message',
            ]);
    }

    public function testItCanGetSingleProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $response = $this->getJson('api/v1/products/' . $product->id);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
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

    public function testItCanCreateNewProduct(): void
    {
        $admin =  User::factory()->create(attributes: [
            'role' => 'admin',
        ]);

        $product = [
            'name' => fake()->name,
            'description' => fake()->text(100),
            'price' => fake()->numberBetween(int1: 100, int2: 1_000),
            'weight' => fake()->numberBetween(int1: 100, int2: 1_000),
            'category' => 'pizza',
        ];

        $response = $this->actingAs($admin)->postJson('api/v1/products', $product);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_CREATED)
            ->assertJsonStructure(structure: [
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

    // public function testRegularUserCannotCreateProduct(): void
    // {
    //     $user =  User::factory()->create();

    //     $product = [
    //         'name' => fake()->name,
    //         'description' => fake()->text(100),
    //         'price' => fake()->numberBetween(int1: 100, int2: 1_000),
    //         'weight' => fake()->numberBetween(int1: 100, int2: 1_000),
    //         'category' => 'pizza',
    //     ];

    //     $response = $this->actingAs($user)->postJson('api/v1/products', $product);
    //     $response
    //         ->assertHeader(headerName: 'Content-Type', value: 'application/json')
    //         ->assertStatus(status: Response::HTTP_FORBIDDEN)
    //         ->assertJsonStructure(structure: [
    //             'message',
    //         ]);
    // }

    public function testItValidatesRequiredFieldsWhenCreatingProduct(): void
    {
        $admin =  User::factory()->create(attributes: [
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->postJson('api/v1/products', ['name' => '']);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(structure: [
                'message',
                'errors',
            ]);
    }

    public function testItCanDeleteExistingProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $admin =  User::factory()->create(attributes: [
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->deleteJson('api/v1/products/' . $product->id);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'message',
            ]);
    }

    public function testItReturns404WhenDeletingNonexistentProduct(): void
    {
        $admin =  User::factory()->create(attributes: [
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->deleteJson('api/v1/products/1000');

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(structure: [
                'message',
            ]);
    }

    public function testItCanUpdateExistingProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $admin =  User::factory()->create(attributes: [
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->patchJson('api/v1/products/' . $product->id, ['name' => 'nameTest']);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'product',
            ]);
    }

    public function testRegularUserCannotUpdateProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $user =  User::factory()->create();

        $response = $this->actingAs($user)->patchJson('api/v1/products/' . $product->id, ['name' => 'nameTest']);
        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_FORBIDDEN)
            ->assertJsonStructure(structure: [
                'message',
            ]);
    }

    public function testItReturns404WhenUpdatingNonexistentProduct(): void
    {
        $admin =  User::factory()->create(attributes: [
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->patchJson('api/v1/products/1000', ['name' => 'nameTest']);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(structure: [
                'message',
            ]);
    }

    public function testItValidatesInvalidCategoryWhenUpdatingProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $admin =  User::factory()->create(attributes: [
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->patchJson('api/v1/products/' . $product->id, ['category' => 'testProduct']);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(structure: [
                'message',
                'errors',
            ]);
    }
}
