<?php

namespace App\Http\Middleware;

use App\Models\OperationLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 操作日志中间件
 * 自动记录管理后台的 POST/PUT/PATCH/DELETE 请求
 */
class LogOperation
{
    /**
     * 不记录日志的路径（前缀匹配）
     */
    protected array $ignoredPaths = [
        'livewire/',
        'livewire-',
        '_debugbar',
        'login',
        'logout',
        'sanctum',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 仅记录写操作且已登录的用户
        if ($this->shouldLog($request) && auth()->check()) {
            $this->log($request);
        }

        return $response;
    }

    protected function shouldLog(Request $request): bool
    {
        $method = $request->method();

        // 只记录写操作
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }

        // 跳过忽略路径
        $path = $request->path();
        foreach ($this->ignoredPaths as $ignored) {
            if (str_starts_with($path, $ignored)) {
                return false;
            }
        }

        return true;
    }

    protected function log(Request $request): void
    {
        try {
            OperationLog::log(
                content: $this->buildContent($request),
                method: $request->method(),
                path: $request->path(),
            );
        } catch (\Throwable) {
            // 日志记录失败不影响业务流程
        }
    }

    protected function buildContent(Request $request): string
    {
        $method = $request->method();
        $path = '/' . $request->path();

        return "{$method} {$path}";
    }
}
