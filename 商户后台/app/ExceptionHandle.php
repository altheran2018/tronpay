<?php
namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        // 参数验证错误
        if ($e instanceof ValidateException) {
            return json([
                'status_code' => 422,
                'message' => $e->getError(),
                'server_time' => $_SERVER['REQUEST_TIME'],
                'request_id' => session_create_id()
            ]);
        }

        // 请求异常
        if ($e instanceof HttpException) {
            return json([
                'error' => $e->getStatusCode(),
                'msg' => $e->getMessage()
            ]);
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }

    
}
