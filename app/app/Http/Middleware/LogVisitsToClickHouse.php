<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogVisitsToClickHouse
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
    
    public function terminate($request, $response)
{
    try {
        $userId = auth()->id() ?? 0;
        $endpoint = $request->path();
        $timestamp = now()->format('Y-m-d H:i:s');
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        
        $sql = "INSERT INTO user_visits (user_id, endpoint, timestamp, ip, user_agent) 
                VALUES ($userId, '$endpoint', '$timestamp', '$ip', '$userAgent')";
        
        DB::connection('clickhouse')->statement($sql);
        
    } catch (\Exception $e) {
        Log::error('ClickHouse logging failed: ' . $e->getMessage());
    }
}
}