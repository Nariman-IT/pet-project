<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupClickHouse extends Command
{
    protected $signature = 'clickhouse:setup';
    protected $description = 'ClickHouse setup';

    public function handle()
{
    try {
        $tables = DB::connection('clickhouse')->select("
            SELECT name FROM system.tables 
            WHERE database = 'laravel_analytics' AND name = 'user_visits'
        ");
        
        if (empty($tables)) {
            DB::connection('clickhouse')->statement("
                CREATE TABLE laravel_analytics.user_visits
                (
                    user_id UInt64,
                    endpoint String,
                    timestamp DateTime,
                    ip String,
                    user_agent String
                )
                ENGINE = MergeTree()
                ORDER BY (timestamp, user_id)
                PARTITION BY toYYYYMM(timestamp)
            ");
            $this->info('Table created');
        } else {
            $this->info('Table already exists');
        }
        
    } catch (\Exception $e) {
        $this->error('Error: ' . $e->getMessage());
    }
}
}