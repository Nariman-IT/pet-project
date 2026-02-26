<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Product\Models\Product;
use App\Cart\Models\CartItem;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::model('product', Product::class);
        Route::model('cartItem', CartItem::class);
    }
}
