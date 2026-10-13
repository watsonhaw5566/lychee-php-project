<?php

declare(strict_types=1);

namespace app\controller;

use app\model\User;
use Lychee\auth\SatokenMiddleware;
use Lychee\http\Controller;
use Lychee\http\JsonResponse;
use Lychee\routing\Middleware;
use Lychee\routing\Route;

/**
 * 控制台简易统计。
 */
#[Route('/api/dashboard')]
#[Middleware(SatokenMiddleware::class)]
class DashboardController extends Controller
{
    /**
     * 用户数统计。
     */
    #[Route('/stats')]
    public function stats(): JsonResponse
    {
        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => [
            'user_total'     => User::count(),
            'admin_total'    => User::where('role', 'admin')->count(),
            'active_total'   => User::where('status', 1)->count(),
            'disabled_total' => User::where('status', 0)->count(),
        ]]);
    }
}
