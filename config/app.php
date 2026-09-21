<?php

use Lychee\http\ExceptionHandler;

return [
    // 时区
    'default_timezone' => 'Asia/Shanghai',
    // 异常错误
    'error_message' => '页面错误，请稍后再试~',
    'show_error_msg' => false,
    'exception_handler' => ExceptionHandler::class,
];
