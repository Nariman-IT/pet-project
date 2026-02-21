<?php

declare(strict_types=1);

namespace App\Product\Providers;

use Illuminate\Support\ServiceProvider;

final class ProductServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(paths: __DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(path: __DIR__ . '/../Routes/api.php');
    }
}
