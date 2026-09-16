<?php

declare(strict_types=1);

return [
    // 模板目录
    'view_path'  => app_path('view'),

    // 模板缓存目录
    'cache_path' => runtime_path('twig'),

    // 是否开启调试（开启后模板变更自动重新编译）
    'debug'      => (bool) env('APP_DEBUG', false),

    // 静态资源基础 URL
    'base_url'   => '',
];
