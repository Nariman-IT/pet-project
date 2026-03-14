<?php

declare(strict_types=1);

use App\Product\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(['api', 'log.visits'])
    ->group(callback: static function (): void {

        Route::prefix('v1')->group(callback: static function (): void {
            Route::get('/products', [ProductController::class, 'index']);
            Route::get('/products/{product}', [ProductController::class, 'show']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::patch('/products/{product}', [ProductController::class, 'update']);
            Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        });
    });
