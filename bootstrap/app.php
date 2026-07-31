<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\LogOperation::class,
        ]);

        // 注册别名，供路由 middleware 选项使用
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // CSRF / Session 过期时：Livewire 请求返回 419（前端自动刷新），普通请求重定向到登录页
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->hasHeader('X-Livewire')) {
                abort(419);
            }

            return redirect()->route('login')->with('status', '页面已过期，请重新登录。');
        });

        // 403 权限不足：Livewire 请求返回 JSON，普通请求渲染自定义 403 页面
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->hasHeader('X-Livewire')) {
                return response()->json(['message' => $e->getMessage() ?: '您没有访问此页面的权限'], 403);
            }

            return response()->view('errors.403', [], 403);
        });
    })->create();
