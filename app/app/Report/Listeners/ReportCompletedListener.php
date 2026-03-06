<?php


namespace App\Report\Listeners;

use App\Report\Events\ReportCompleted;
// use App\Notifications\ReportReadyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ReportCompletedListener implements ShouldQueue
{
    public $connection = 'rabbitmq';
    public $queue = 'reports_completed_queue';

    public function handle(ReportCompleted $event): void
    {
        $report = $event->report;
    }
}