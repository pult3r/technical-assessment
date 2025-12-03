<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QrCode Render Backend
    |--------------------------------------------------------------------------
    |
    | Simple QrCode domyślnie korzysta z Imagick, jeśli jest dostępny.
    | W Docker PHP-FPM imagick NIE JEST zainstalowany.
    |
    | Dlatego wymuszamy backend PNG (GD), który działa zawsze.
    |
    */

    'renderer' => 'png',

    /*
    |--------------------------------------------------------------------------
    | Default QrCode Settings
    |--------------------------------------------------------------------------
    */

    'size' => 300,
    'format' => 'png',
    'margin' => 2,
    'error_correction' => 'H',
];
