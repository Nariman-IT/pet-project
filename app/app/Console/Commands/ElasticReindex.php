<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Product\Models\Product;
use App\Services\Elasticsearch\ProductIndexer;
use Illuminate\Console\Command;

final class ElasticReindex extends Command
{
    protected $signature = 'elastic:reindex 
        {--chunk=100 : Number of products to process per batch}
        {--force : Force reindex without confirmation}';
    
    protected $description = 'Reindex all products to Elasticsearch';

    public function handle(ProductIndexer $indexer): int
    {
        $total = Product::count();

        if ($total === 0) {
            $this->warn('No products to index.');
            return Command::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm("Found {$total} products. Do you want to reindex them?")) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->info('Starting reindexing of products...');
        
        $chunkSize = (int) $this->option('chunk');
        $processed = 0;
        $errors = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        Product::chunk($chunkSize, function ($products) use ($indexer, $bar, &$processed, &$errors) {
            foreach ($products as $product) {
                try {
                    $indexer->indexProduct($product);
                    $processed++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("\nError indexing product ID {$product->id}: " . $e->getMessage());
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Successfully indexed: {$processed}");
        
        if ($errors > 0) {
            $this->warn("Failed: {$errors}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}