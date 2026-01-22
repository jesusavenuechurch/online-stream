<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RTMP Server Configuration
    |--------------------------------------------------------------------------
    */

    'rtmp_url' => env('RTMP_URL', 'rtmp://localhost/live'),
    'rtmp_server_ip' => env('RTMP_SERVER_IP', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | HLS Configuration
    |--------------------------------------------------------------------------
    */

    'hls_path' => env('HLS_PATH', storage_path('app/public/hls')),
    'hls_url' => env('HLS_URL', env('APP_URL') . '/hls'),

    /*
    |--------------------------------------------------------------------------
    | Recording Configuration
    |--------------------------------------------------------------------------
    */

    'recordings_path' => env('RECORDINGS_PATH', storage_path('app/public/recordings')),
    'recordings_url' => env('RECORDINGS_URL', env('APP_URL') . '/recordings'),

    /*
    |--------------------------------------------------------------------------
    | Stream Quality Settings
    |--------------------------------------------------------------------------
    */

    'default_quality' => [
        'resolution' => '1280x720',
        'bitrate' => '2500k',
        'framerate' => 30,
    ],
];