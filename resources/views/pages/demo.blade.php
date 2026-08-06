<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo - 本地速送服务平台</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground antialiased">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">本地速送服务平台 · Demo</h1>
                <p class="text-muted-foreground mt-1">UI 组件库 + 业务示例</p>
            </div>
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700">登录后台</a>
        </div>
        <livewire:demo.demo-page />
    </div>
</body>
</html>
