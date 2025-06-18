<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    //
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cashier_id' => 'required',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'transaction_number' => 'TRX-' . strtoupper(uniqid()),
            'cashier_id' => $validatedData['cashier_id'],
            'total_price' => collect($validatedData['items'])->sum(function ($item) {
                return Product::find($item['product_id'])->price * $item['quantity'];
            }),
            'total_item' => collect($validatedData['items'])->sum('quantity'),
            'payment_method' => $request->input('payment_method', 'cash'), // Default to 'cash' if not provided
        ]);

        foreach ($validatedData['items'] as $item) {
            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'total_price' => Product::find($item['product_id'])->price * $item['quantity'],
            ]);
        }

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order->load('orderItems.product'),
        ], 201);
    }
}
