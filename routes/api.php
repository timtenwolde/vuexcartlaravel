<?php

use App\Cart;
use App\Product;
use Illuminate\Http\Request;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, PUT, PATCH, DELETE, GET, OPTIONS');

Route::delete('/cart', function () {
    Cart::truncate();
});

Route::delete('/cart/{productId}', function ($productId, Request $request) {
    $item = Cart::where('product_id', $productId)->first();
    $item->decrement('quantity');

    if ($item->quantity === 0) {
        $item->delete();
    }

    return response(null, 200);
});

Route::post('/cart', function (Request $request) {
    $item = Cart::where('product_id', $request->product_id);

    if ($item->count()) {
        $item->increment('quantity');
        $item = $item->first();
    } else {
        $item = Cart::forceCreate([
            'product_id' => $request->product_id,
            'quantity' => 1,
        ]);
    }

    return response()->json([
        'quantity' => $item->quantity,
        'product' => $item->product
    ]);
});

Route::get('/cart', function () {
    return Cart::with('product')->orderBy('created_at', 'desc')->get();
});

Route::get('/products', function () {
    return Product::get();
});
