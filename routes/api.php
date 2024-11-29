<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\LogEndpointRequests;
use App\Models\EndpointStat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum',LogEndpointRequests::class])->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::apiResource('products', ProductController::class);
    Route::get('/products/{category}', [ProductController::class, 'getProductByCategory']);
    Route::get('/categories', [CategoryController::class,'index']);
    Route::get('/categories/{category}/products', [CategoryController::class, 'getProductsByCategory']);

    Route::get('search/products', [ProductController::class,'search']);

});


Route::post('/register', [AuthController::class,'register']);

Route::post('/login', [AuthController::class,'login']);

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
})->middleware('auth:sanctum');


Route::get('/stats', function () {
    return response()->json(EndpointStat::all());
});

Route::get('/stats/{endpoint}', function ($endpoint) {
    $stats = EndpointStat::where('endpoint',  'LIKE', "%{$endpoint}%")->get();

    return response()->json($stats);
});
