<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analytics;
    
    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }
    
    public function visits(Request $request)
    {
        $days = $request->get('days', 7);
        
        return response()->json([
            'success' => true,
            'data' => $this->analytics->getVisitsChart($days)
        ]);
    }
    
    public function peakHours()
    {
        return response()->json([
            'success' => true,
            'data' => $this->analytics->getPeakHours()
        ]);
    }
    
    public function popularEndpoints(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        return response()->json([
            'success' => true,
            'data' => $this->analytics->getPopularEndpoints($limit)
        ]);
    }
}