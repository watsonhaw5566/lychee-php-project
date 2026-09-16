<?php

declare(strict_types=1);

/**
 * 应用 Web 入口。
 *
 * 适用于 Apache / Nginx，也可作为 PHP 内置开发服务器的路由脚本。
 */

if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

$basePath = dirname(__DIR__);

require $basePath . '/vendor/autoload.php';

use Lychee\Application;

$app = new Application(
    basePath: $basePath,
    controllerNamespace: 'App\\controller',
);

$app->run();
