<?php

declare(strict_types=1);

return [
    // 默认磁盘
    'default' => 'aliyun',

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
        'aliyun' => [
            'type'          => 'aliyun',
            'access_id'     => env('OSS_ACCESS_KEY_ID', ''),
            'access_secret' => env('OSS_ACCESS_KEY_SECRET', ''),
            'bucket'        => env('OSS_BUCKET', ''),
            'endpoint'      => env('OSS_ENDPOINT', 'oss-cn-hangzhou.aliyuncs.com'),
            'cdn'           => env('OSS_CDN', ''),
        ],
        'qcloud' => [
            'type'       => 'qcloud',
            'app_id'     => env('COS_APP_ID', ''),
            'secret_id'  => env('COS_SECRET_ID', ''),
            'secret_key' => env('COS_SECRET_KEY', ''),
            'region'     => env('COS_REGION', 'ap-guangzhou'),
            'bucket'     => env('COS_BUCKET', ''),
            'cdn'        => env('COS_CDN', ''),
        ],
    ],
];