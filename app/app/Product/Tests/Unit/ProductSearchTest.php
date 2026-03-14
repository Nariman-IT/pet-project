<?php

namespace App\Product\Tests\Unit;

use App\Product\Models\Product;
use App\Services\Elasticsearch\ProductIndexer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private ProductIndexer $indexer;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        $this->indexer = app(ProductIndexer::class);
        
        $this->createTestIndex();
        $this->seedTestProducts();
    }

    private function createTestIndex(): void
    {
        try {
            $client = app(\Elastic\Elasticsearch\Client::class);
            $indexName = config('elasticsearch.indexes.products.name');
            
            if ($client->indices()->exists(['index' => $indexName])->asBool()) {
                $client->indices()->delete(['index' => $indexName]);
            }

            $client->indices()->create([
                'index' => $indexName,
                'body' => [
                    'settings' => config('elasticsearch.indexes.products.settings'),
                    'mappings' => config('elasticsearch.indexes.products.mappings')
                ]
            ]);
        } catch (\Exception $e) {
            $this->fail('Failed to create test index: ' . $e->getMessage());
        }
    }

    private function seedTestProducts(): void
    {
        $products = [
            [
                'name' => 'Пицца Маргарита',
                'description' => 'Классическая пицца с томатным соусом, моцареллой и базиликом',
                'price' => 45000,
                'weight' => 500,
                'category' => 'pizza'
            ],
            [
                'name' => 'Пицца Пепперони',
                'description' => 'Острая пицца с пикантной пепперони',
                'price' => 55000,
                'weight' => 550,
                'category' => 'pizza'
            ],
            [
                'name' => 'Coca-Cola 0.5л',
                'description' => 'Классический газированный напиток',
                'price' => 15000,
                'weight' => 500,
                'category' => 'drink'
            ],
            [
                'name' => 'Сок апельсиновый 1л',
                'description' => 'Натуральный апельсиновый сок',
                'price' => 32000,
                'weight' => 1000,
                'category' => 'drink'
            ]
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            $this->indexer->indexProduct($product);
        }

        sleep(1);
    }

    #[Test]
    public function it_can_search_products_with_fuzzy_matching_via_api(): void
    {
        $response = $this->getJson('/api/v1/products?search=пица');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'products' => [
                    '*' => ['id', 'name', 'description', 'price', 'category']
                ],
                'meta' => ['total', 'page', 'search', 'took_ms']
            ]);

        $this->assertGreaterThanOrEqual(2, $response->json('meta.total'));
        
        $products = $response->json('products');
        $names = array_column($products, 'name');
        
        $this->assertContains('Пицца Маргарита', $names);
        $this->assertContains('Пицца Пепперони', $names);
    }
    #[Test]    public function it_returns_empty_result_for_non_existent_search(): void
    {
        $response = $this->getJson('/api/v1/products?search=asdfghjkl');

        $response->assertStatus(200)
            ->assertJson([
                'products' => [],
                'meta' => ['total' => 0]
            ]);
    }

    #[Test]
    public function search_results_are_cached(): void
    {
        $response1 = $this->getJson('/api/v1/products?search=пица');
        
        $this->assertNotNull($response1->json('products'));
        $firstCount = count($response1->json('products') ?? []);
        
        $newProduct = Product::create([
            'name' => 'Новая пицца',
            'description' => 'Свежая пицца',
            'price' => 60000,
            'weight' => 600,
            'category' => 'pizza'
        ]);
        $this->indexer->indexProduct($newProduct);
        
        $response2 = $this->getJson('/api/v1/products?search=пица');
        
        $this->assertNotNull($response2->json('products'));
        $this->assertEquals(
            $firstCount,
            count($response2->json('products') ?? [])
        );
        
        Cache::tags(['products_search'])->flush();
        
        $response3 = $this->getJson('/api/v1/products?search=пица');
        
        $this->assertNotNull($response3->json('products'));
        $this->assertGreaterThan(
            $firstCount,
            count($response3->json('products') ?? [])
        );
    }
}