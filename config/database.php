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
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8mb4',
            'prefix'   => env('DB_PREFIX', ''),
            // 断线后是否自动重连（默认 false）
            'break_reconnect' => false,
            // 是否监听 SQL（默认 true）
            'trigger_sql' => true,
        ],

        'sqlite' => [
            'type'     => 'sqlite',
            'database' => env('DB_DATABASE', runtime_path('app.sqlite')),
            'prefix'   => env('DB_PREFIX', ''),
        ],

        // 全局配置
        // 是否自动写入时间戳（默认 true）
        'auto_timestamp' => true,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 全局时间字段名，默认值：create_time,update_time
        'datetime_field' => '',
    ],
];
