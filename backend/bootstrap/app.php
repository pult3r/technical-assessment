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
        |--------------------------------------------------------------------------
        | Global Middleware Aliases
        |--------------------------------------------------------------------------
        |
        | These middleware aliases allow us to use short names like:
        | 'jwt.auth'      -> JWT authentication & token validation
        | 'mysql.user'    -> Sets MySQL session variable @user_id based on JWT
        |
        */

        $middleware->alias([
            'jwt.auth'   => \App\Http\Middleware\JwtAuth::class,
            'mysql.user' => \App\Http\Middleware\SetMysqlUserId::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
