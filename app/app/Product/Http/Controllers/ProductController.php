<?php

declare(strict_types=1);

namespace App\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Product\Http\Requests\ProductDestroyRequest;
use App\Product\Http\Requests\ProductStoreRequest;
use App\Product\Http\Requests\ProductUpdateRequest;
use App\Product\Http\Resources\ProductResource;
use App\Product\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Elasticsearch\ProductIndexer;

final class ProductController extends Controller
{
    public function __construct(
        private readonly ProductIndexer $productIndexer
    ) {}

    public function index(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');
        
        if ($search) {
            $cacheKey = 'search.' . md5($search) . '.page.' . $page;
            
            $result = Cache::tags(['products_search'])->remember(
                $cacheKey, 
                3600, 
                fn() => $this->productIndexer->search($search, 10, $page)
            );
            
            if (empty($result['ids'])) {
                return response()->json([
                    'products' => [],
                    'meta' => [
                        'total' => 0,
                        'page' => $page,
                        'search' => $search,
                        'took_ms' => $result['took'] ?? 0
                    ]
                ]);
            }
            
            $products = Product::whereIn('id', $result['ids'])
                ->orderByRaw('array_position(ARRAY[' . implode(',', $result['ids']) . ']::bigint[], id)')
                ->get();
            
            return response()->json([
                'products' => ProductResource::collection($products),
                'meta' => [
                    'total' => $result['total'],
                    'page' => $page,
                    'search' => $search,
                    'took_ms' => $result['took'] ?? 0
                ]
            ]);
        }
        
        $cacheKey = 'page.' . $page;
        $products = Cache::tags(['products'])->remember(
            $cacheKey, 
            3600, 
            fn() => Product::paginate(10, ['*'], 'page', $page)
        );

        return response()->json([
            'products' => ProductResource::collection($products),
            'meta' => [
                'total' => $products->total(),
                'page' => $page,
                'has_more' => $products->hasMorePages()
            ]
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'product' => new ProductResource(resource: $product),
        ], Response::HTTP_OK);
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'product' => new ProductResource(resource: $product),
        ], Response::HTTP_CREATED);
    }

    public function update(Product $product, ProductUpdateRequest $request): JsonResponse
    {
        $collection = $request->safe()->collect();
        $data = $collection->reject(callback: static fn($value, $key) => $value === null);


        if ($data->isEmpty()) {
            return response()->json([
                'product' => new ProductResource(resource: $product),
            ], Response::HTTP_OK);
        }

        $product->update(attributes: $data->all());

        return response()->json([
            'product' => new ProductResource(resource: $product),
        ], Response::HTTP_OK);
    }

    public function destroy(Product $product, ProductDestroyRequest $request): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Продукт удален',
        ], Response::HTTP_OK);
    }
}
