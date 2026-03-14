<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getVisitsChart($days = 7)
    {
        $result = DB::connection('clickhouse')->select("
            SELECT 
                toDate(timestamp) as date,
                count(*) as total_visits,
                uniqExact(user_id) as unique_visitors
            FROM laravel_analytics.user_visits
            WHERE timestamp >= now() - INTERVAL ? DAY
            GROUP BY date
            ORDER BY date
        ", [$days]);
        
        return $result;
    }
    
    public function getPeakHours()
    {
        $result = DB::connection('clickhouse')->select("
            SELECT 
                toHour(timestamp) as hour,
                count(*) as visits
            FROM laravel_analytics.user_visits
            WHERE timestamp >= now() - INTERVAL 7 DAY
            GROUP BY hour
            ORDER BY hour
        ");
        
        return $result;
    }
    
    public function getPopularEndpoints($limit = 10)
    {
        $result = DB::connection('clickhouse')->select("
            SELECT 
                endpoint,
                count(*) as visits
            FROM laravel_analytics.user_visits
            WHERE timestamp >= now() - INTERVAL 7 DAY
            GROUP BY endpoint
            ORDER BY visits DESC
            LIMIT ?
        ", [$limit]);
        
        return $result;
    }
}