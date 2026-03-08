<?php 

namespace App\Listeners;

use App\Events\ExceptionThrown;
use App\Exceptions\TranslatableException;
use Illuminate\Http\JsonResponse;

class RenderExceptionListener
{
    public function handle(ExceptionThrown $event): ?JsonResponse
    {
        $exception = $event->exception;
        
        if ($exception instanceof TranslatableException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getHttpStatusCode()
                ]
            ], $exception->getHttpStatusCode());  // ← 404
        }
        
        return null;
    }
}