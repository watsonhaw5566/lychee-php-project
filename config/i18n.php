<?php

declare(strict_types=1);

return [
    // 默认语言
    'locale'          => env('APP_LOCALE', 'zh-CN'),

    // 回退语言：当前语言找不到翻译时使用
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    // 翻译文件根目录
    'path'            => base_path('app/lang'),
];
