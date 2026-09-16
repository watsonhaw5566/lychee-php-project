<?php

declare(strict_types=1);

return [
    // 默认日志频道
    'default'  => env('LOG_CHANNEL', 'file'),

    // 日志频道列表
    'channels' => [
        'file' => [
            'type'  => 'file',
            'path'  => runtime_path('log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],
    ],
];
