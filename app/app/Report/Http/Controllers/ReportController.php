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
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function store(GenerateReportRequest $request): JsonResponse
    {
        $report = Report::create(attributes: [
            'status' => Report::STATUS_PENDING,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        GenerateReportJob::dispatch(
 $report->id,
            $report->start_date,
            $report->end_date
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
                'created_at' => $report->created_at->toIso8601String(),
                'completed_at' => $report->completed_at?->toIso8601String(),
                'error_message' => $report->error_message,
                'can_download' => $report->status === Report::STATUS_COMPLETED
            ]
        ]);
    }

   
    public function download(string $id)
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
        
        return Storage::disk('s3')->download($report->file_path);
    }
}