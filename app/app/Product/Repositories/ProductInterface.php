<?php

namespace App\Product\Repositories;
use App\Product\Models\Product;

interface ProductInterface {
    public function findAll();
    public function findById(int $id);
    public function create(array $data);
    public function update(Product $product, array $data);
    public function delete(Product $product);
}