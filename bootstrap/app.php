<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            \App\Http\Middleware\EnsureJsonResponse::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 404 errors
        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'message' => 'Record not found.'
            ], 404);
        });

        // Handle authorization errors
        $exceptions->renderable(function (AuthorizationException $e) {
            return response()->json([
                'message' => 'Forbidden. You do not have access to this resource.'
            ], 403);
        });

        // Handle validation errors
        $exceptions->renderable(function (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        });

        // Handle general HTTP exceptions
        $exceptions->renderable(function (HttpException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'An error occurred.',
            ], $e->getStatusCode());
        });

    })->create();
