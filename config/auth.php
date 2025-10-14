<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        // Guard utama untuk user umum
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Guard untuk artist
        'artist' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Guard untuk client
        'client' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Untuk API (jika kamu pakai token atau sanctum)
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    'providers' => [
        // Provider tunggal untuk semua user
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
