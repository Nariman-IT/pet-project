<?php

namespace App\Report\Console\Commands;

use App\Report\Jobs\GenerateReportJob;
use App\Report\Models\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateReportCommand extends Command
{
    // Можно вызывать: php artisan report:generate 2024-01-01 2024-01-31
    protected $signature = 'report:generate 
                            {startDate? : Начальная дата (Y-m-d)} 
                            {endDate? : Конечная дата (Y-m-d)}
                            {--type=daily : Тип отчета (daily/manual)}';

    
    protected $description = 'Генерирует отчет о проданных товарах за период';



    public function handle(): int
    {

        // Получаем значения аргументов, переданные в команду при её вызове.
        $startDate = $this->argument('startDate');
        $endDate = $this->argument('endDate');
        
        // Если даты не переданы - берем вчерашний день
        if (!$startDate || !$endDate) {
            $startDate = Carbon::yesterday()->toDateString();
            $endDate = Carbon::yesterday()->toDateString();
            
            $this->warn("Даты не указаны, генерирую за вчера: {$startDate}");
        }

        // Валидируем даты
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            
            if ($start->gt($end)) {
                $this->error('Начальная дата не может быть позже конечной!');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error(' Неверный формат даты! Используйте Y-m-d');
            return Command::FAILURE;
        }


        
        $report = Report::create([
            'status' => Report::STATUS_PENDING,
            'type' => $this->option('type'),
            'start_date' => $start,
            'end_date' => $end,
            'metadata' => [
                'generated_by' => 'command',
                'user_id' => null,
                'command' => 'report:generate'
            ]
        ]);


        // Отправляем задание в очередь RabbitMQ
        GenerateReportJob::dispatch(
            $report->id,
            $start->toDateString(),
            $end->toDateString()
        );

        return Command::SUCCESS;
    }
}