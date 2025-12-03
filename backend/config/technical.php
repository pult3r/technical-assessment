<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Application URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('APP_URL', 'http://localhost:8080'),

    /*
    |--------------------------------------------------------------------------
    | JWT Secret
    |--------------------------------------------------------------------------
    | MUST match .env variable JWT_SECRET
    */
    'jwt_secret' => env('JWT_SECRET', 'change-me'),

    /*
    |--------------------------------------------------------------------------
    | JWT Expiration (seconds)
    |--------------------------------------------------------------------------
    */
    'jwt_exp' => env('APP_JWT_EXP', 3600),

    /*
    |--------------------------------------------------------------------------
    | Login Credentials (demo)
    |--------------------------------------------------------------------------
    | Used ONLY if you have a basic auth fallback
    */
    'login' => [
        'username' => env('LOGIN_USERNAME', 'admin'),
        'password' => env('LOGIN_PASSWORD', 'password123')
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Target URL
    |--------------------------------------------------------------------------
    | MUST match the key expected in PdfController:
    | config('technical.qr_target_url')
    */
    'qr_target_url' => env('QR_TARGET_URL', 'https://student-cribs.com/'),

    /*
    |--------------------------------------------------------------------------
    | Default directory for generated PDFs
    |--------------------------------------------------------------------------
    | Not used by PdfController (it uses Storage::disk('public')),
    | but left here for consistency.
    */
    'generated_dir' => env('GENERATED_DIR', 'generated'),
];
