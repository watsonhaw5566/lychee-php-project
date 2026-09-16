<?php

declare(strict_types=1);

namespace App\middleware;

use Closure;
use Lychee\http\HttpException;
use Lychee\http\MiddlewareInterface;
use Lychee\http\Request;
use Lychee\http\Response;

/**
 * 鉴权中间件示例。
 *
 * 通过 #[Middleware(AuthMiddleware::class)] 挂载到控制器或方法。
 * 校验通过后将当前登录 ID 写入请求，供控制器或数据权限 trait 使用。
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if ($token === null || $token === '') {
            throw new HttpException(401, 'Unauthorized.');
        }

        // TODO: 根据 token 解析用户 ID，这里仅作示例
        $userId = 1;
        $request->setLoginId($userId);

        return $next($request);
    }
}
