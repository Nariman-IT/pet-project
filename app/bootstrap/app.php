<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Для ошибки 404
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'message' => 'Not found',
            ], Response::HTTP_NOT_FOUND);
        });

        // Для ошибки 422
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([  
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        // Для ошибки 403
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            return response()->json([
                'message' => 'Forbidden',
            ], Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (QueryException $e, Request $request) {
            return response()->json([
                'message' => 'Not found',
            ], Response::HTTP_NOT_FOUND);
        });

    })->create();
