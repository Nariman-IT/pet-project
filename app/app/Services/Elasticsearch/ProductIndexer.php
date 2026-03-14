<?php
declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Product\Models\Product;
use Elastic\Elasticsearch\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

final class ProductIndexer
{
    public function __construct(
        private Client $elasticsearch
    ) {}

    public function indexProduct(Product $product): void
    {
        $this->elasticsearch->index([
            'index' => Config::get('elasticsearch.indexes.products.name'),
            'id' => (string) $product->id,
            'body' => $this->productToArray($product),
        ]);

        Cache::tags(['products_search'])->flush();
    }

    public function deleteProduct(Product $product): void
    {
        try {
            $this->elasticsearch->delete([
                'index' => Config::get('elasticsearch.indexes.products.name'),
                'id' => (string) $product->id,
            ]);

            Cache::tags(['products_search'])->flush();
        } catch (\Exception $e) {
           
            if (!$this->isDocumentMissingException($e)) {
                throw $e;
            }
        }
    }

    public function updateProduct(Product $product): void
    {
        try {
            $this->elasticsearch->update([
                'index' => Config::get('elasticsearch.indexes.products.name'),
                'id' => (string) $product->id,
                'body' => [
                    'doc' => $this->productToArray($product)
                ]
            ]);

            Cache::tags(['products_search'])->flush();
        } catch (\Exception $e) {
            if ($this->isDocumentMissingException($e)) {
                $this->indexProduct($product);
            } else {
                throw $e;
            }
        }
    }

    public function search(string $query, int $perPage = 10, int $page = 1): array
    {
        $from = ($page - 1) * $perPage;

        $params = [
            'index' => Config::get('elasticsearch.indexes.products.name'),
            'size' => $perPage,
            'from' => $from,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
            
                            [
                                'match' => [
                                    'name' => [
                                        'query' => $query,
                                        'boost' => 4,
                                        'fuzziness' => 'AUTO',
                                        'operator' => 'and'
                                    ]
                                ]
                            ],

                            [
                                'match' => [
                                    'name' => [
                                        'query' => $query,
                                        'boost' => 3,
                                        'fuzziness' => 'AUTO',
                                        'minimum_should_match' => '75%'
                                    ]
                                ]
                            ],
                           
                            [
                                'match' => [
                                    'description' => [
                                        'query' => $query,
                                        'boost' => 2,
                                        'fuzziness' => 'AUTO'
                                    ]
                                ]
                            ],
                           
                            [
                                'term' => [
                                    'category' => [
                                        'value' => $this->mapQueryToCategory($query),
                                        'boost' => 1
                                    ]
                                ]
                            ]
                        ],
                        'minimum_should_match' => 1
                    ]
                ],

                'sort' => [
                    '_score',
                    ['price' => 'asc']
                ]
            ]
        ];

        $results = $this->elasticsearch->search($params);
        
        return [
            'ids' => collect($results['hits']['hits'])->pluck('_id')->map('intval')->toArray(),
            'total' => $results['hits']['total']['value'],
            'took' => $results['took'] ?? 0 
        ];
    }


    private function isDocumentMissingException(\Exception $e): bool
    {
        return str_contains($e->getMessage(), 'document_missing_exception');
    }

   
    private function productToArray(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description ?? '',
            'price' => (float) $product->price, // кастуем к float
            'weight' => (float) ($product->weight ?? 0),
            'category' => $product->category,
            'created_at' => $product->created_at?->toIso8601String(),
            'updated_at' => $product->updated_at?->toIso8601String(),
        ];
    }

    private function mapQueryToCategory(string $query): ?string
    {
        $query = mb_strtolower($query);
        
        $pizzaKeywords = ['пицца', 'пиццу', 'пицц', 'pizza', 'питца'];
        $drinkKeywords = ['напиток', 'напитки', 'пить', 'drink', 'вода', 'cola', 'кока', 'кола'];
        
        foreach ($pizzaKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                return 'pizza';
            }
        }
        
        foreach ($drinkKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                return 'drink';
            }
        }
        
        return null;
    }
}