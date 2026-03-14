<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AnalyticsController;

Route::prefix('admin/analytics')->group(function () {
    Route::get('/visits', [AnalyticsController::class, 'visits']);
    Route::get('/peak-hours', [AnalyticsController::class, 'peakHours']);
    Route::get('/popular-endpoints', [AnalyticsController::class, 'popularEndpoints']);
});