<?php

namespace App\Report\Console\Commands;

use App\Report\Jobs\GenerateReportJob;
use App\Report\Models\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;


class GenerateReportCommand extends Command
{

    protected $signature = 'reports:dispatch-daily';
    protected $description = 'Dispatch the daily report generation job';

    public function handle()
    {
        $report = Report::create([
            'start_date' => Carbon::yesterday()->startOfDay(),
            'end_date' => Carbon::yesterday()->endOfDay(),
            'status' => 'pending',
        ]);

        GenerateReportJob::dispatch(
            $report->id,
            $report->start_date,
            $report->end_date
        )->onQueue('reports.generate');

        $this->info('Daily report job dispatched successfully.');
    }
}