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

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 час на генерацию
    public $tries = 3;       // 3 попытки

    protected string $reportId;
    protected string $startDate;
    protected string $endDate;

    public function __construct(string $reportId, string $startDate, string $endDate)
    {
        $this->reportId = $reportId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        
        // Отправляем в exchange reports.generate
        $this->onConnection('rabbitmq');
        $this->onQueue('reports_generation_queue');
    }

    public function handle(): void
    {
        try {
            // Обновляем статус
            $report = Report::findOrFail($this->reportId);
            $report->update(['status' => Report::STATUS_PROCESSING]);

            // Читаем заказы за период
            $orders = Order::with('user')
                ->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ])
                ->where('status', 'completed')
                ->get();

            if ($orders->isEmpty()) {
                throw new \Exception('Нет заказов за указанный период');
            }

            $fileName = "reports/{$this->reportId}.jsonl";
            $tempFile = tempnam(sys_get_temp_dir(), 'report_');
            $handle = fopen($tempFile, 'w');
            
            $totalRecords = 0;

            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    // Формируем строку в нужном формате
                    $record = [
                        'product_name' => $item->product_name,
                        'price' => (float) $item->price,
                        'amount' => $item->quantity,
                        'user' => [
                            'id' => $order->user_id
                        ]
                    ];
                    
                    fwrite($handle, json_encode($record) . PHP_EOL);
                    $totalRecords++;
                }
            }

            fclose($handle);

            // Загружаем в MinIO
            $fileContent = file_get_contents($tempFile);
            Storage::disk('s3')->put($fileName, $fileContent, 'private');
            unlink($tempFile); // Удаляем временный файл

            // Обновляем статус отчета
            $report->markAsCompleted($fileName, $totalRecords);

            //  Публикуем событие о завершении в exchange reports.completed
            event(new ReportCompleted($report));

        } catch (\Throwable $e) {
            // Обновляем статус на failed
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