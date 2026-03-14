<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Product\Models\Product;
use App\Services\Elasticsearch\ProductIndexer;
use Illuminate\Console\Command;

final class ElasticTestSearch extends Command
{
    protected $signature = 'elastic:test-search {query : Search query}';
    protected $description = 'Test fuzzy search in Elasticsearch';

    public function handle(ProductIndexer $indexer): int
    {
        $query = $this->argument('query');
        
        $this->info("Searching for: '{$query}'");
        $this->newLine();
        
        $results = $indexer->search($query, 20);
        
        if (empty($results['ids'])) {
            $this->warn('No results found.');
            return Command::SUCCESS;
        }
        
        $this->line("Found: {$results['total']} products (took: {$results['took']}ms)");
        $this->newLine();
        
        $products = Product::whereIn('id', $results['ids'])
            ->orderByRaw('array_position(ARRAY[' . implode(',', $results['ids']) . ']::bigint[], id)')
            ->get();
        
        $this->table(
            ['ID', 'Name', 'Category', 'Price', 'Score'],
            $products->map(fn($p) => [
                $p->id,
                $p->name,
                $p->category,
                $p->price / 100 . ' ₽',
                array_search($p->id, $results['ids']) + 1
            ])
        );
        
        return Command::SUCCESS;
    }
}