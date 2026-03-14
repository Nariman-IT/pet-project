<?php

declare(strict_types=1);

namespace App\Product\Models\Traits;

use App\Services\Elasticsearch\ProductIndexer;
use Illuminate\Support\Facades\App;

trait SyncsWithElasticsearch
{
    protected static function bootSyncsWithElasticsearch(): void
    {
        static::saved(function ($model) {
            App::make(ProductIndexer::class)->updateProduct($model);
        });

        static::deleted(function ($model) {
            App::make(ProductIndexer::class)->deleteProduct($model);
        });
    }
}