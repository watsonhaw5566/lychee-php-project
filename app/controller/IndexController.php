<?php

declare(strict_types=1);

namespace App\controller;

use Lychee\http\Response;
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

    #[Route('/test')]
    public function test()
    {
       return  $a;
    }
}
