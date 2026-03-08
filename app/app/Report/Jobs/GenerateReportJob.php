<?php

namespace App\Report\Jobs;

use App\Order\Models\Order;
use App\Report\Models\Report;
use App\Report\Events\ReportCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $reportId;
    protected string $startDate;
    protected string $endDate;

    public function __construct(string $reportId, string $startDate, string $endDate)
    {
        $this->reportId = $reportId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
       
        $this->onConnection('rabbitmq');
        $this->onQueue('reports_generation_queue');
    }

    public function handle(): void
    {
        try {
            $report = Report::findOrFail($this->reportId);
            $report->update(['status' => Report::STATUS_PROCESSING]);

            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();

            /** @var \Illuminate\Support\Collection $orders */
            $orders = Order::whereBetween('created_at', [$start, $end])
                ->where('status', 'created')
                ->get();


            if ($orders->isEmpty()) {
                throw new \Exception('Нет заказов за указанный период');
            }

            $fileName = "reports/{$this->reportId}.jsonl";
            $tempFile = tempnam(sys_get_temp_dir(), 'report_');
            $handle = fopen($tempFile, 'w');
            

            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $record = [
                        'product_name' => $item->product_name,
                        'price' => (float) $item->price,
                        'amount' => $item->quantity,
                        'user' => [
                            'id' => $order->user_id
                        ]
                    ];
                    
                    fwrite($handle, json_encode($record) . PHP_EOL);
                    
                }
            }

            fclose($handle);

            Log::info('Temp file created', [
            'path' => $tempFile,
            'size' => filesize($tempFile)
        ]);
          
            $fileContent = file_get_contents($tempFile);



        Log::info('Attempting to write to MinIO', [
            'bucket' => env('AWS_BUCKET'),
            'file' => $fileName
        ]);


            $result = Storage::disk('s3')->put($fileName, $fileContent, 'private');
            

        Log::info('MinIO write result', [
            'success' => $result,
            'file_exists' => Storage::disk('s3')->exists($fileName)
        ]);

            unlink($tempFile);

            $report->markAsCompleted($fileName);

            event(new ReportCompleted($report));

        } catch (\Throwable $e) {
            if (isset($report)) {
                $report->markAsFailed($e->getMessage());
            };

            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Job провалился после всех попыток', [
            'report_id' => $this->reportId,
            'error' => $e->getMessage()
        ]);
    }
}