<?php

declare(strict_types=1);

use App\Cart\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('api')
    ->group(callback: static function (): void {

        Route::prefix('v1')->group(callback: static function (): void {
            Route::get('/cart', [CartController::class, 'index']);
            Route::post('/cart', [CartController::class, 'store']);
            Route::patch('/cart/{cartItem}', [CartController::class, 'update']);
            Route::delete('/cart/{id}', [CartController::class, 'destroy']);
            Route::delete('/cart', [CartController::class, 'clear']);
        });
    });