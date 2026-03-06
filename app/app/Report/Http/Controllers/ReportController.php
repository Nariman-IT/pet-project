<?php

namespace App\Report\Http\Controllers;

use App\Report\Http\Requests\GenerateReportRequest;
use App\Report\Jobs\GenerateReportJob;
use App\Report\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function store(GenerateReportRequest $request): JsonResponse
    {
        $report = Report::create([
            'status' => Report::STATUS_PENDING,
            'type' => 'manual',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'metadata' => [
                'requested_by' => auth()->id(),
                'requested_at' => now()->toIso8601String()
            ]
        ]);

        GenerateReportJob::dispatch(
            $report->id,
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'data' => [
                'report_id' => $report->id,
                'status' => $report->status,
                'message' => 'Отчет поставлен в очередь на генерацию'
            ]
        ], 202); 
    }


    public function show(string $id): JsonResponse
    {
        $report = Report::findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $report->id,
                'status' => $report->status,
                'type' => $report->type,
                'period' => [
                    'start' => $report->start_date->toDateString(),
                    'end' => $report->end_date->toDateString()
                ],
                'total_records' => $report->total_records,
                'created_at' => $report->created_at->toIso8601String(),
                'completed_at' => $report->completed_at?->toIso8601String(),
                'error_message' => $report->error_message,
                'can_download' => $report->status === Report::STATUS_COMPLETED
            ]
        ]);
    }

   
    public function download(string $id): Response|StreamedResponse
    {
        $report = Report::findOrFail($id);

        if ($report->status !== Report::STATUS_COMPLETED) {
            return response()->json([
                'message' => 'Отчет еще не готов или не существует'
            ], 404);
        }

        if (!Storage::disk('s3')->exists($report->file_path)) {
            return response()->json([
                'message' => 'Файл отчета не найден'
            ], 404);
        }

        
        return redirect()->away($report->file_url);
    }
}