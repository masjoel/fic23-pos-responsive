<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    //index
    public function index()
    {
        $products = Product::with('category')->get();
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }
}
