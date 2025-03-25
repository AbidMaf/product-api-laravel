<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index() {
        return response()->json($this->productService->getAll());
    }

    public function show($id) {
        return response()->json([
            'message' => 'Product Retrieved',
            'data' => $this->productService->findById($id)
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_category_id' => 'required|exists:product_category,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $image = $request->hasFile('image');
        $imageName = null;
        if($image) {
            $imageFile = $request->file('image');
            $imageName = $imageFile->hashName();
            $imageFile->storeAs('public/storage/products', $imageName);
        }

        $data = [
            'product_category_id' => $request->product_category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imageName
        ];
        
        return response()->json([
            'message' => 'Product Created',
            'data' => $this->productService->create($data)
        ]);
    }

    public function update(Request $request, $id) {
        if ($request->isJson()) {
            return response()->json(['error' => 'Use multipart/form-data for file uploads'], 400);
        }
    
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'product_category_id' => 'required|exists:product_category,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = $this->productService->findById($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $image = $request->hasFile('image');
        $imageName = null;
        if($image) {
            if($product->image) {
                Storage::delete('public/storage/products/' . $product->image);
            }

            $imageFile = $request->file('image');
            $imageName = $imageFile->hashName();
            $imageFile->storeAs('public/storage/products', $imageName);
        }

        $data = [
            'product_category_id' => $request->product_category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imageName
        ];

        return response()->json([
            'message' => 'Product Updated',
            'data' => $this->productService->update($id, $data)
        ]);
    }

    public function destroy($id) {
        $delete = $this->productService->delete($id);
        if(!$delete) {
            return response()->json([
                'message' => 'Product Not Found',
                'data' => null
            ]);
        }
        return response()->json([
            'message' => 'Product Deleted',
            'data' => null
        ]);
    }
}
