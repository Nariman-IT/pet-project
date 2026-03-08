<?php

namespace App\Events;

use App\Exceptions\TranslatableException;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;
use App\Exceptions\ReportNotFoundException;

class ExceptionThrown
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ReportNotFoundException $exception;
    public ?string $locale;

    public function __construct(ReportNotFoundException $exception, ?string $locale = null)
    {
        $this->exception = $exception;
        $this->locale = $locale ?? app()->getLocale();
    }
}