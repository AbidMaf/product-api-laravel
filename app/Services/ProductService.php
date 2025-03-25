<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getAll()
    {
        return Product::with('category')->get();
    }

    public function findById($id)
    {
        return Product::with('category')->find($id);
    }

    public function create(array $data)
    {
        $product = Product::create($data);
        return $product->load('category');
    }

    public function update($id, array $data)
    {
        $product = Product::find($id);
        if (!$product) {
            return null;
        }

        $product->update($data);
        return $product->load('category');
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }
}