<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login endpoint returning JWT.
     */
    public function login(Request $request)
    {
        // Only POST allowed
        if (!$request->isMethod('post')) {
            return response()->json([
                'success' => false,
                'error' => __('messages.method_not_allowed')
            ], 405);
        }

        // Validate input
        $username = $request->input('username');
        $password = $request->input('password');

        if (!$username || !$password) {
            return response()->json([
                'success' => false,
                'error' => __('messages.login_missing_fields')
            ], 400);
        }

        // Load credentials from config
        $configUser = config('technical.login.username');
        $configPass = config('technical.login.password');

        // Compare credentials
        if ($username !== $configUser || $password !== $configPass) {
            return response()->json([
                'success' => false,
                'error' => __('messages.login_invalid')
            ], 401);
        }

        // Generate JWT
        $secret = config('technical.jwt_secret');
        $exp = time() + config('technical.jwt_exp');

        $payload = [
            'sub' => $username,
            'iat' => time(),
            'exp' => $exp
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return response()->json([
            'success' => true,
            'token' => $token,
            'expires_at' => $exp
        ]);
    }
}
