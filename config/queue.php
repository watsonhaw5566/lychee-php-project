<?php

declare(strict_types=1);

return [
    // 默认队列连接
    'default'     => env('QUEUE_CONNECTION', 'sync'),

    // 队列连接列表
    'connections' => [
        'sync'  => [
            'type' => 'sync',
        ],

        'redis' => [
            'type'        => 'redis',
            'host'        => env('REDIS_HOST', '127.0.0.1'),
            'port'        => (int) env('REDIS_PORT', 6379),
            'password'    => env('REDIS_PASSWORD', ''),
            'select'      => 0,
            'timeout'     => 5,
            'persistent'  => false,
            'queue'       => 'default',
            'retry_after' => 60,
        ],
    ],
];
