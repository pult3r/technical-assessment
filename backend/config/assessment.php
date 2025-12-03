<?php

return [
    'jwt_secret' => env('JWT_SECRET', 'supersecret123456'),
    'jwt_expiration' => env('APP_JWT_EXP', 3600),

    'qr_target_url' => 'https://student-cribs.com/',
];
