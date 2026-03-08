<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Product\Providers\ProductServiceProvider::class,
    App\Order\Providers\OrderServiceProvider::class,
    App\Cart\Providers\CartServiceProvider::class,
    App\Auth\Providers\AuthServiceProvider::class,
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    App\Report\Providers\ReportServiceProvider::class,
];
