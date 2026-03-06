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
use Symfony\Component\HttpFoundation\Response;

final class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = $request->get(key: 'page', default: 1);

        $products = Product::paginate(10, ['*'], 'page', $page);

        return response()->json([
            'products' => ProductResource::collection(resource: $products),
        ], Response::HTTP_OK);
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
