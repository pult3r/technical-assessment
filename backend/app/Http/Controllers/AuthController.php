<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * LOGIN
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

        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => __('messages.login_invalid')
            ], 401);
        }

        return $this->makeJwtResponse($user);
    }

    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|unique:users',
            'email'    => 'required|email|max:100|unique:users',
            'password' => 'required|min:6|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors()->first()
            ], 422);
        }

        // utworzenie usera
        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => $request->password, 
        ]);

        return $this->makeJwtResponse($user);
    }

    /**
     * JWT generator
     */
    private function makeJwtResponse(User $user)
    {
        $secret = config('technical.jwt_secret');
        $exp    = time() + config('technical.jwt_exp');

        $payload = [
            'sub' => $user->id,
            'username' => $user->username,
            'iat' => time(),
            'exp' => $exp
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return response()->json([
            'success' => true,
            'token' => $token,
            'expires_at' => $exp,
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);
    }
}
