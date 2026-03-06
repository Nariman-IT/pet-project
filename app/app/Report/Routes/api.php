<?php

use App\Report\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('reports')->group(function () {
    Route::post('/', [ReportController::class, 'store']);
    Route::get('{id}', [ReportController::class, 'show']);
    Route::get('{id}/download', [ReportController::class, 'download']);
});