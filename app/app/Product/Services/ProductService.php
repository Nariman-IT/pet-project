<?php

namespace App\Product\Services;

use App\Product\Repositories\ProductRepository\ProductRepository;
use App\Product\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use App\Product\Http\Resources\ProductResource;


class ProductService
{
    private ProductRepository $product;

    public function __construct()
    {
        $this->product = new ProductRepository();
    }


    public function getAll($paginate): JsonResponse
    {
        $products = Product::paginate(10, ['*'], 'page', $paginate);
        return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
            ], Response::HTTP_OK);
    }



    public function getById(int $id): JsonResponse
    {
        $product = $this->product->findById($id);

        if(!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Продукт не найден',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ], Response::HTTP_OK);
    }



    public function store(array $data): JsonResponse
    {
        $product = $this->product->create($data);

        if(!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создание продукта',
                'data' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ], Response::HTTP_CREATED);
    }




    public function update(int $id, Collection $data): JsonResponse
    {
        $product = $this->product->findById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Продукт не найден',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        if ($data->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ], Response::HTTP_OK);
        }

        $product = $this->product->update($product, $data->all());

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления продукта',
                'data' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ], Response::HTTP_OK);
    }



    public function destroy(int $id): JsonResponse
    {
        $product = $this->product->findById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Продукт не найден',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $product = $this->product->delete($product);
        
        return response()->json([
                'success' => true,
                'message' => 'Продукт удален',
            ], Response::HTTP_NO_CONTENT);
    }
}
