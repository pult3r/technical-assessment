<?php

return [

    // Base application URL
    'base_url' => env('APP_URL', 'http://localhost:8080'),

    // JWT secret
    'jwt_secret' => env('APP_JWT_SECRET', 'change-me-secret'),

    // JWT expiration time (seconds)
    'jwt_exp' => env('APP_JWT_EXP', 3600),

    // Login credentials (DEMO) – in real scenario use database
    'login' => [
        'username' => env('LOGIN_USERNAME', 'admin'),
        'password' => env('LOGIN_PASSWORD', 'password123')
    ],

    // URL for QR
    'qr_target' => env('QR_TARGET_URL', 'https://student-cribs.com/'),

    // Directory for generated PDFs
    'generated_dir' => env('GENERATED_DIR', 'generated'),
];
