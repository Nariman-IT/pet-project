<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Product\Providers\ProductServiceProvider::class,
    App\Auth\Providers\AuthServiceProvider::class,
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
];
