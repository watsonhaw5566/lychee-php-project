<?php

declare(strict_types=1);

return [
    // Token 名称（请求头 / Cookie 中的字段名）
    'token_name'      => 'Authorization',

    // Cookie 中的 token 字段名
    'token_cookie_name' => 'satoken',

    // Token 读取驱动：header / cookie / chain
    // chain：先从 Header 读取，再从 Cookie 读取，兼顾 SPA 与传统页面跳转
    'token_reader'    => 'cookie',

    // Token 存储驱动，null 表示使用默认（内存）
    'store'           => null,

    // Token 有效期（秒），默认 7 天
    'timeout'         => 86400 * 7,

    // 是否自动续期
    'auto_renew'      => true,

    // 到期前多少秒内自动续期
    'renew_before'    => 3600,

    // 同一账号最大登录数
    'max_login_count' => 10,
];
