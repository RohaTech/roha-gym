<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:8081',
        'http://127.0.0.1:8081',
        'http://localhost:8082',
        'http://127.0.0.1:8082',
        'http://localhost:8083',
        'http://127.0.0.1:8083',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'exp://localhost:8081',
        'exp://127.0.0.1:8081',
        'exp://localhost:8082',
        'exp://127.0.0.1:8082',
        'exp://localhost:8083',
        'exp://127.0.0.1:8083',
        'https://rohatechs.com',
        'http://rohatechs.com',
        'http://localhost:5173'
    ],

    'allowed_origins_patterns' => [
        '/^http:\/\/localhost:\d+$/',
        '/^http:\/\/127\.0\.0\.1:\d+$/',
        '/^exp:\/\/.*$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
