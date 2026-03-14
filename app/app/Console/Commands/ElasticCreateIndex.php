<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

final class ElasticCreateIndex extends Command
{
    protected $signature = 'elastic:create-index';
    protected $description = 'Create Elasticsearch index for products';

    public function handle(Client $client): int
    {
        $indexConfig = Config::get('elasticsearch.indexes.products');
        $indexName = $indexConfig['name'];

        if ($client->indices()->exists(['index' => $indexName])->asBool()) {
            if (!$this->confirm("Index '{$indexName}' already exists. Do you want to delete and recreate it?")) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }

            $client->indices()->delete(['index' => $indexName]);
            $this->warn("Index '{$indexName}' deleted.");
        }

        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => $indexConfig['settings'],
                'mappings' => $indexConfig['mappings']
            ]
        ];

        try {
            $response = $client->indices()->create($params);
            
            if ($response['acknowledged'] ?? false) {
                $this->info("✓ Index '{$indexName}' created successfully!");
                
                $this->line("Settings and mappings applied.");
                
                $mapping = $client->indices()->getMapping(['index' => $indexName]);
                $this->line("Mapping: " . json_encode($mapping[$indexName]['mappings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $this->error("Failed to create index.");
            }
        } catch (\Exception $e) {
            $this->error("Error creating index: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}