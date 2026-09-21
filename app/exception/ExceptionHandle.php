<?php

namespace app\exception;

use Lychee\http\ExceptionHandler;
use Lychee\http\HttpException;
use Lychee\http\Request;
use Lychee\http\Response;
use Lychee\routing\RouteNotFoundException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use Throwable;

class ExceptionHandle extends ExceptionHandler
{
    /**
     * 不需要记录日志的异常类列表。
     *
     * @var array<class-string<Throwable>>
     */
    protected array $ignoreReport = [
        HttpException::class,
        RouteNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常日志。
     */
    public function report(Throwable $e): void
    {
        parent::report($e);
        // 可在此处上报到监控平台
    }

    /**
     * 渲染异常响应。
     */
    public function render(Request $request, Throwable $e): Response
    {
        // todo 自定义业务异常处理
        // 其余交给父类处理
        return parent::render($request, $e);
    }
}
