<?php
// app/Providers/ScheduleServiceProvider.php

namespace App\Providers;

use App\Jobs\GenerateReportJob;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Ежедневный отчет в 02:00
        Schedule::call(function () {
            $this->generateDailyReport();
        })->dailyAt('02:00')->name('daily-report')->withoutOverlapping();
        
        // Альтернативный вариант: проверка новых сообщений в RabbitMQ каждые 5 минут
        Schedule::call(function () {
            $this->checkRabbitMQMessages();
        })->everyFiveMinutes()->name('rabbitmq-check');
    }
    
    private function generateDailyReport(): void
    {
        $date = Carbon::yesterday();
        
        $report = Report::create([
            'status' => Report::STATUS_PENDING,
            'type' => 'daily',
            'start_date' => $date->copy()->startOfDay(),
            'end_date' => $date->copy()->endOfDay(),
            'metadata' => [
                'generated_by' => 'scheduler',
                'scheduled_at' => now()->toIso8601String()
            ]
        ]);
        
        GenerateReportJob::dispatch(
            $report->id,
            $report->start_date->toDateString(),
            $report->end_date->toDateString()
        );
        
        logger("✅ Daily report {$report->id} queued for {$date->toDateString()}");
    }
    
    private function checkRabbitMQMessages(): void
    {
        // Альтернатива: можно проверять специальную очередь
        // для триггера генерации отчетов через RabbitMQ
        logger('🔍 Checking RabbitMQ for report generation triggers...');
    }
}