<?php

namespace App\Listeners;

use App\Events\ExceptionThrown;
use App\Exceptions\TranslatableException;
use Illuminate\Support\Facades\Log;


class TranslateExceptionListener
{
    public function handle(ExceptionThrown $event): void
    {
        $exception = $event->exception;
        $locale = $event->locale;
        
        if ($exception instanceof TranslatableException) {
            $exception->setLocale($locale);
            

            $reflection = new \ReflectionClass($exception);
            $messageProperty = $reflection->getParentClass()->getProperty('message');
            $messageProperty->setValue($exception, $exception->getRawTranslation());
        }
    }
}