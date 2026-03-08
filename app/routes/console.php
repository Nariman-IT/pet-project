<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::command('reports:dispatch-daily')
         ->dailyAt('02:00')
         ->timezone('Europe/Moscow')
         ->withoutOverlapping()
         ->runInBackground();