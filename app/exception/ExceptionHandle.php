<?php

namespace app\exception;

use Lychee\http\ExceptionHandler;
use Lychee\http\HttpException;
use Lychee\http\Request;
use Lychee\http\Response;
use Lychee\routing\RouteNotFoundException;
use Lychee\validate\ValidateException;
use Throwable;

/**
 * 应用异常处理器。
 *
 */
class ExceptionHandle extends ExceptionHandler
{
    /**
     * 不需要记录日志的异常类列表。
     *
     * 所有内置业务异常都继承自 HttpException，因此默认不会写入日志。
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
        // 校验异常始终返回 JSON，保留字段级错误
        if ($e instanceof ValidateException) {
            return $this->renderBusinessError($e);
        }

        // 浏览器请求交由父类渲染 HTML 异常页
        if ($this->shouldRenderHtml($request)) {
            return parent::render($request, $e);
        }

        // JSON 请求：仅业务异常按项目统一结构输出，系统异常沿用父类处理
        if ($e instanceof HttpException) {
            return $this->renderBusinessError($e);
        }

        return parent::render($request, $e);
    }

    /**
     * 将业务异常渲染为项目统一的 JSON 响应。
     */
    protected function renderBusinessError(HttpException $e): Response
    {
        $status = $e->getStatusCode();
        $debug  = (bool) config('app.debug', env('APP_DEBUG', false));

        $payload = [
            'errno' => $e->getCode(),
            'code'  => $status,
            'msg'   => $e->getMessage() ?: $this->statusText($status),
            'data'  => null,
        ];

        // 校验异常：保留字段级错误映射
        if ($e instanceof ValidateException) {
            $errors = $e->getError();

            if (is_array($errors)) {
                $payload['errors'] = $errors;
            } elseif ($e->getKey() !== null) {
                $payload['errors'] = [$e->getKey() => (string) $errors];
            }
        }

        // 调试模式附加异常详情
        if ($debug) {
            $payload['type']  = $e::class;
            $payload['file']  = $e->getFile();
            $payload['line']  = $e->getLine();
            $payload['trace'] = explode("\n", $e->getTraceAsString());
        }

        return json($payload, $status, $e->getHeaders());
    }
}
