<?php

declare(strict_types=1);

return [
    // 默认磁盘
    'default' => 'local',

    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => runtime_path('storage'),
        ],
        'public' => [
            'type' => 'local',
            'root' => public_path('storage'),
            'url'  => '/storage',
        ],
        'oss'    => [
            'type'            => 'oss',
            'access_key_id'   => env('OSS_ACCESS_KEY_ID', ''),
            'access_key_secret' => env('OSS_ACCESS_KEY_SECRET', ''),
            'bucket'          => env('OSS_BUCKET', ''),
            'endpoint'        => env('OSS_ENDPOINT', ''),
            'prefix'          => '',
        ],
        'cos'    => [
            'type'      => 'cos',
            'region'    => env('COS_REGION', ''),
            'app_id'    => env('COS_APP_ID', ''),
            'secret_id' => env('COS_SECRET_ID', ''),
            'secret_key' => env('COS_SECRET_KEY', ''),
            'bucket'    => env('COS_BUCKET', ''),
            'prefix'    => '',
        ],
    ],
];
