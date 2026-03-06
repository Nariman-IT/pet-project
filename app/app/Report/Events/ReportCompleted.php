<?php


namespace App\Report\Events;

use App\Report\Models\Report;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
        
        // Для RabbitMQ указываем exchange
        $this->onConnection('rabbitmq');
    }

    /**
     * Данные для отправки в exchange reports.completed
     */
    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->report->id,
            'status' => $this->report->status,
            'file_path' => $this->report->file_path,
            'total_records' => $this->report->total_records,
            'completed_at' => $this->report->completed_at?->toIso8601String(),
            'period' => [
                'start' => $this->report->start_date->toDateString(),
                'end' => $this->report->end_date->toDateString()
            ]
        ];
    }
}