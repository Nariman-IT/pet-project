<?php

declare(strict_types=1);

use App\Order\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('api')
    ->group(callback: static function (): void {

        Route::prefix('v1')->group(callback: static function (): void {
            Route::post('/orders', [OrderController::class, 'store']);
            Route::get('/orders', [OrderController::class, 'index']);
            Route::get('/orders/{order}', [OrderController::class, 'show']);
            Route::patch('/orders/{order} ', [OrderController::class, 'update']);
            Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);
        });
    });