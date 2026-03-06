<?php
// routes/console.php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use Carbon\Carbon;

// Ежедневная генерация отчета в 02:00
Schedule::call(function () {
    $date = Carbon::yesterday();
    
    logger('📊 Запуск ежедневной генерации отчета', ['date' => $date->toDateString()]);
    
    // Создаем запись об отчете
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
    
    // Отправляем в очередь RabbitMQ
    GenerateReportJob::dispatch(
        $report->id,
        $report->start_date->toDateString(),
        $report->end_date->toDateString()
    );
    
    logger('✅ Отчет поставлен в очередь', ['report_id' => $report->id]);
})
->dailyAt('02:00')
->withoutOverlapping()
->onSuccess(function () {
    logger('✅ Ежедневный отчет успешно сгенерирован');
})
->onFailure(function () {
    logger('❌ Ошибка генерации ежедневного отчета');
});

// Можно добавить дополнительные задания
Schedule::command('report:clean-old')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->description('Очистка старых отчетов');

// Альтернатива: можно использовать Artisan команду
Schedule::command('report:generate:daily')
    ->dailyAt('02:00')
    ->withoutOverlapping();