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
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
                $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
                $this->app->register(TelescopeServiceProvider::class);
        }
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
