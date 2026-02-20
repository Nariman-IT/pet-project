<?php

use App\Product\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('api')
    ->group(function () {
        
        Route::prefix('v1')->group(function () {
            Route::get('/products', [ProductController::class, 'index']);
            Route::get('/products/{product}', [ProductController::class, 'show']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::patch('/products/{product}', [ProductController::class, 'update']);
            Route::delete('/products/{product}', [ProductController::class, 'destroy']); 
        }); 
});