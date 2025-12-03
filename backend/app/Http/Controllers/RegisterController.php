<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Firebase\JWT\JWT;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
                'details' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        
        $secret = config('technical.jwt_secret');
        $exp = time() + config('technical.jwt_exp');

        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => $exp
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return response()->json([
            'success' => true,
            'message' => __('messages.register.success'),
            'token' => $token,     
            'expires_at' => $exp   
        ]);
    }
}
