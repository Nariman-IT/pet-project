<?php

namespace App\Product\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product\Models\Product;
use App\Product\Http\Requests\ProductRequest;
use App\Product\Http\Requests\UpdateProductRequest;
use App\Product\Services\ProductService;
use Symfony\Component\HttpFoundation\Response;


class ProductController extends Controller
{
    
    public function index(Request $request, ProductService $service) 
    {
        $page = $request->get('page', 1);
        $products = $service->getAll($page);
        return $products;
    }


    public function show(int $id, ProductService $service) 
    {
        $product = $service->getById($id);
        return $product;
    }


    public function store(ProductRequest $request, ProductService $service) 
    {
        $collection = $request->safe()->collect();
        $data = $collection->reject(function ($value, $key) {
            return $value === null;
        });

        $product = $service->store($data->all());
        return $product;
    }


    public function update(int $id, UpdateProductRequest $request, ProductService $service) 
    {
        $collection = $request->safe()->collect();
        $data = $collection->reject(function ($value, $key) {
            return $value === null;
        });
       
        $product = $service->update($id, $data);
        return $product;
    }


    public function destroy(int $id, ProductService $service) 
    {
        $product = $service->destroy($id);
        return $product;
    }
}
