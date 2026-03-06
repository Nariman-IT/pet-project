<?php

use App\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('api')
    ->group(function () {
        
        Route::prefix('v1/auth/')->group(function () {
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout']);
        }); 
});