<?php

namespace App\Providers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * @throws AuthenticationException
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            $host = sprintf(
                '%s://%s:%s',
                env('ELASTICSEARCH_SCHEME', 'http'),
                env('ELASTICSEARCH_HOST', 'elasticsearch'),
                env('ELASTICSEARCH_PORT', '9200')
            );

            return ClientBuilder::create()
                ->setHosts([$host])
                ->setRetries(2)
                ->setElasticMetaHeader(false)
                ->build();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}