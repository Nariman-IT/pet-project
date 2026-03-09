<?php

declare(strict_types=1);

use App\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('api')
    ->group(callback: static function (): void {

        Route::prefix('v1')->group(callback: static function (): void {
            Route::get('/admin/users', [AdminController::class, 'index']);
            Route::post('/admin/{id}/update', [AdminController::class, 'update']);
        });
    });
