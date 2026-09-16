<?php

declare(strict_types=1);

return [
    // 驱动：file
    'driver'    => 'file',

    // 文件驱动存储路径
    'path'      => runtime_path('session'),

    // Session Cookie 名称
    'name'      => 'LYCHEE_SESSION',

    // 过期时间（分钟）
    'expire'    => 120,

    // Cookie 路径
    'cookie_path'  => '/',

    // Cookie 域名
    'domain'    => null,

    // 是否仅 HTTPS 传输
    'secure'    => false,

    // 是否禁止 JS 访问
    'http_only' => true,

    // SameSite 策略：lax / strict / none
    'same_site' => 'lax',
];