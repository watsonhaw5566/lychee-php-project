<?php

declare(strict_types=1);

namespace App\controller;

use Lychee\http\JsonResponse;
use Lychee\http\Response;
use Lychee\i18n\I18nMiddleware;
use Lychee\routing\Middleware;
use Lychee\routing\Route;

/**
 * 首页控制器。
 */
class IndexController
{
    #[Route('/')]
    public function index(): Response
    {
        $html = view('index.html', [
            'url' => 'https://lychee-php.watsonhaw.top/',
        ]);

        return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    #[Route('/hello/{name}')]
    #[Middleware(I18nMiddleware::class)]
    public function hello(string $name): JsonResponse
    {
        return new JsonResponse([
            'message' => lang('messages.welcome', ['name' => $name]),
        ]);
    }

    #[Route('/ip')]
    public function ip(): JsonResponse
    {
        return new JsonResponse([
            'ip' => request()->ip,
        ]);
    }

    #[Route('/error')]
    public function error()
    {
        return $a;
    }
}
