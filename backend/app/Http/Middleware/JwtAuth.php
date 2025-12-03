<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * - Checks Authorization: Bearer <token>
     * - Decodes JWT using config('technical.jwt_secret') and HS256
     * - Attaches decoded payload to request attributes as 'jwt_payload'
     * - Returns 401 when missing/invalid
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json([
                'success' => false,
                'error' => __('messages.unauthorized')
            ], 401);
        }

        $token = $matches[1];

        try {
            $secret = config('technical.jwt_secret', env('JWT_SECRET'));
            if (!$secret) {
                throw new \Exception('JWT secret not configured.');
            }

            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Optionally attach decoded token (payload) to request for controllers
            $request->attributes->set('jwt_payload', $decoded);

            return $next($request);
        } catch (\Throwable $e) {
            // Log exception if needed: \Log::warning('JWT decode error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'error' => __('messages.invalid_token')
            ], 401);
        }
    }
}
