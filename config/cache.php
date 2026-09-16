<?php

declare(strict_types=1);

return [
    // 默认缓存驱动
    'default' => env('CACHE_DRIVER', 'file'),

    // 缓存驱动列表
    'stores'  => [
        'file' => [
            'type'          => 'File',
            'path'          => runtime_path('cache'),
            'expire'        => 0,
            'prefix'        => '',
            'cache_subdir'  => true,
            'hash_type'     => 'md5',
            'data_compress' => false,
        ],

        'redis' => [
            'type'       => 'Redis',
            'host'       => env('REDIS_HOST', '127.0.0.1'),
            'port'       => (int) env('REDIS_PORT', 6379),
            'password'   => env('REDIS_PASSWORD', ''),
            'select'     => 0,
            'expire'     => 0,
            'prefix'     => '',
            'timeout'    => 0,
            'persistent' => false,
        ],
    ],
];
