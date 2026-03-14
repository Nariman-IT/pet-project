<?php

declare(strict_types=1);

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

final class AdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(path: __DIR__ . '/../Routes/api.php');
    }
}
