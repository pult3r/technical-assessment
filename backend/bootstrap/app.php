<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php'
    )
    ->withMiddleware(function (Middleware $middleware) {
        /*
         * Register route middleware aliases.
         *
         * This maps the short name 'jwt.auth' used in routes to the full
         * middleware class that handles JWT validation.
         */
        $middleware->alias([
            'jwt.auth' => \App\Http\Middleware\JwtAuth::class,
        ]);

        /*
         * If you want to append global middleware (run on every request),
         * you can use $middleware->append(...) here, for example:
         *
         * $middleware->append(\App\Http\Middleware\SomeGlobalMiddleware::class);
         */
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
