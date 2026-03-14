<?php

return [
    App\Admin\Providers\AdminServiceProvider::class,
    App\Auth\Providers\AuthServiceProvider::class,
    App\Cart\Providers\CartServiceProvider::class,
    App\Order\Providers\OrderServiceProvider::class,
    App\Product\Providers\ProductServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\ElasticsearchServiceProvider::class,
    App\Report\Providers\ReportServiceProvider::class,
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
];
