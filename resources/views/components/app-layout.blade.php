<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', '本地速送') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- 注入后端 UI 配置到前端 Alpine.js --}}
    <script>
        window.__UI_CLOSE_ON_OUTSIDE = {{ \App\Support\Setting::get('ui_close_on_outside', true) ? 'true' : 'false' }};
    </script>
</head>
<body class="bg-background text-foreground min-h-screen font-sans">
    <div class="flex min-h-screen flex-col">
        {{-- 顶部导航栏 --}}
        <x-app-topnav />

        {{-- 主内容区 --}}
        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Toast 全局容器 --}}
    <x-ui.toaster />

    @livewireScripts
</body>
</html>
