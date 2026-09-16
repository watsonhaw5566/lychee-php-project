<?php

declare(strict_types=1);

return [
    // 默认连接
    'default'     => env('DB_CONNECTION', 'mysql'),

    // 连接列表
    'connections' => [
        'mysql' => [
            'type'     => 'mysql',
            'hostname' => env('DB_HOST', '127.0.0.1'),
            'hostport' => (int) env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'app'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8mb4',
            'prefix'   => env('DB_PREFIX', ''),
            'debug'    => false,
        ],

        'sqlite' => [
            'type'     => 'sqlite',
            'database' => env('DB_DATABASE', runtime_path('app.sqlite')),
            'prefix'   => env('DB_PREFIX', ''),
            'debug'    => false,
        ],
    ],
];
