<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class CheckElasticsearchConnection extends Command
{
    protected $signature = 'elastic:check';
    protected $description = 'Check Elasticsearch connection';

    public function handle(Client $client)
    {
        try {
            $response = $client->info();
            
            $this->info('✓ Connected to Elasticsearch successfully!');
            $this->line('Cluster: ' . $response['cluster_name']);
            $this->line('Version: ' . $response['version']['number']);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Failed to connect to Elasticsearch: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}