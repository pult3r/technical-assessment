<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base App URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('APP_URL', 'http://localhost:8080'),

    /*
    |--------------------------------------------------------------------------
    | JWT
    |--------------------------------------------------------------------------
    */
    'jwt_secret' => env('APP_JWT_SECRET', 'change-me-secret'),
    'jwt_exp'    => env('APP_JWT_EXP', 3600),

    /*
    |--------------------------------------------------------------------------
    | Demo login (only for testing)
    |--------------------------------------------------------------------------
    */
    'login' => [
        'username' => env('LOGIN_USERNAME', 'admin'),
        'password' => env('LOGIN_PASSWORD', 'password123'),
    ],

    /*
    |--------------------------------------------------------------------------
    | QR code configuration
    |--------------------------------------------------------------------------
    */
    'qr' => [
        'target_url'       => env('QR_TARGET_URL', 'https://student-cribs.com/'),
        'size'             => env('QR_SIZE', 300),
        'margin'           => env('QR_MARGIN', 1),
        'error_correction' => env('QR_ECC', 'H'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Generation Settings
    |--------------------------------------------------------------------------
    */
    'pdf' => [
        'storage_dir'      => env('PDF_DIR', 'pdf'),
        'paper_format'     => env('PDF_PAPER', 'A4'),
        'paper_orientation'=> env('PDF_ORIENT', 'portrait'),
        'css_path'         => resource_path('views/pdf/style.css'),
    ],
];
