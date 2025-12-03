<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetMysqlUserId
{
    /**
     * Set @user_id in the current MySQL session
     */
    public function handle(Request $request, Closure $next)
    {
        $payload = $request->attributes->get('jwt_payload');

        // For unauthenticated routes (login, register)
        if (!$payload || !isset($payload->sub)) {
            DB::statement("SET @user_id = NULL");
            return $next($request);
        }

        $userId = (int)$payload->sub;

        // Set user id in MYSQL session
        DB::statement("SET @user_id = {$userId}");

        return $next($request);
    }
}
