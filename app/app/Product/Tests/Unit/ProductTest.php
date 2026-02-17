<?php

namespace App\Product\Tests\Unit;

use Tests\TestCase;
use App\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $connectionsToTransact = ['pgsql'];

    public function test_it_can_get_all_products(): void
    {
        $response = $this->getJson('api/v1/products');
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }

    
    public function test_it_returns_404_when_product_not_found(): void
    {
        $response = $this->getJson('api/v1/products/10000');
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Продукт не найден',
                'data' => null,
            ]);
    }

 
    public function test_it_can_get_single_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Тестовый продукт',
            'description' => 'Тестовое описание',
            'price' => 1000,
            'weight' => 100,
            'category' => 'pizza',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('api/v1/products/'. $product->id);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [ 'id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'price' => $product->price,
                            'weight' => $product->weight,
                            'category' => $product->category,
                            'created_at' => $product->created_at->toJSON(),
                            'updated_at' => $product->updated_at->toJSON(),],
            ]);
    }



    public function test_it_can_create_new_product(): void
    {
        $product = Product::factory()->raw();

        $response = $this->postJson('api/v1/products', $product);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(201)
            ->assertValid()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'weight' => $product['weight'],
                    'category' => $product['category'],
                ]
            ]);
    }




    public function test_it_validates_required_fields_when_creating_product(): void
    {
        $response = $this->postJson('api/v1/products', ['name' => '']);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(422)
            ->assertInvalid(['name', 'price', 'category'])
            ->assertJson([
                'success' => false,
                'message' => 'Ошибка валидации',
            ]);
    }



    public function test_it_can_delete_existing_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson('api/v1/products/' . $product->id);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Продукт удален',
            ]);
    }



    public function test_it_returns_404_when_deleting_nonexistent_product(): void
    {
        $response = $this->deleteJson('api/v1/products/1000');
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Продукт не найден',
                'data' => null,
            ]);
    }



    public function test_it_can_update_existing_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->patchJson('api/v1/products/'. $product->id, ['name' => 'nameTest']);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'nameTest'
                ]
            ]);
    }


    public function test_it_returns_404_when_updating_nonexistent_product(): void
    {
        $response = $this->patchJson('api/v1/products/1000', ['name' => 'nameTest']);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Продукт не найден',
                'data' => null,
            ]);
    }


    public function test_it_validates_invalid_category_when_updating_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->patchJson('api/v1/products/' . $product->id, ['category' => 'testProduct']);
        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(422)
            ->assertInvalid(['category'])
            ->assertJson([
                'success' => false,
                'message' => 'Ошибка валидации',
            ]);
    }
}

