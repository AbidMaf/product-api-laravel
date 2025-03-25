<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductCategoryService;

class ProductCategoryController extends Controller
{
    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

    public function index() {
        return response()->json($this->productCategoryService->getAll());
    }

    public function show($id) {
        return response()->json([
            'message' => 'Product Category Retrieved',
            'data' => $this->productCategoryService->findById($id)
        ]);
    }

    public function store(Request $request) {
        $data = $request->all();
        return response()->json([
            'message' => 'Product Category Created',
            'data' => $this->productCategoryService->create($data)
        ]);
    }

    public function update(Request $request, $id) {
        $data = $request->all();
        return response()->json([
            'message' => 'Product Category Updated',
            'data' => $this->productCategoryService->update($id, $data)
        ]);
    }

    public function destroy($id) {
        $delete = $this->productCategoryService->delete($id);
        if(!$delete) {
            return response()->json([
                'message' => 'Product Category Not Found',
                'data' => null
            ]);
        }
        return response()->json([
            'message' => 'Product Category Deleted',
            'data' => null
        ]);
    }
}
