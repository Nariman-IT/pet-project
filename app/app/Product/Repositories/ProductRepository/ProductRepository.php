<?php

namespace App\Product\Repositories\ProductRepository;

use App\Product\Repositories\ProductInterface;
use App\Product\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductRepository implements ProductInterface
{
    public function findAll()
    {
        //
    }

    
    public function findById(int $id): Product|null
    {
        return Product::find($id);
    }



    public function create(array $data): Product|bool
    {
        DB::beginTransaction();

        try {
            $product = Product::create($data);
            DB::commit();
            return $product;
            
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Ошибка при создание продукта', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }




    public function update(Product $product, array $data): Product|bool
    {
        DB::beginTransaction();

        try {
            $product->update($data);
            DB::commit();
            return $product;
            
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Ошибка при обновления продукта', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }


    public function delete(Product $product)
    {
        return $product->delete();
    }
}