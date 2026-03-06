<?php

declare(strict_types=1);

use App\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('api')
    ->group(callback: static function (): void {

        Route::prefix('v1/auth')->group(callback: static function (): void {
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
