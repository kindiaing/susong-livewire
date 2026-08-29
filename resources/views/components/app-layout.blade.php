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

    {{-- 注入后端配置 --}}
    @php
        try {
            $uiCloseOnOutside = \App\Support\Setting::get('ui_close_on_outside', true);
            $contentWidth = \App\Models\UserPreference::getPreference(auth()->id(), 'content_width') ?? 'default';
            $widthClass = match($contentWidth) {
                'wide' => 'max-w-[1600px]',
                'full' => 'max-w-none px-12',
                default => 'max-w-7xl',
            };
        } catch (\Throwable) {
            $uiCloseOnOutside = true;
            $contentWidth = 'default';
            $widthClass = 'max-w-7xl';
        }
    @endphp
    <script>
        window.__UI_CLOSE_ON_OUTSIDE = {{ $uiCloseOnOutside ? 'true' : 'false' }};
        window.__UI_CONTENT_WIDTH = '{{ $contentWidth ?? 'default' }}';
        // 注入 Laravel 用户信息供 Echo 认证使用
        window.Laravel = { userId: {{ auth()->id() ?? 'null' }} };
    </script>
</head>
<body class="bg-background text-foreground min-h-screen font-sans">
    <div class="flex min-h-screen flex-col">
        {{-- 顶部导航栏 --}}
        <x-app-topnav />

        {{-- 主内容区 --}}
        <main class="flex-1">
            <div x-data id="content-wrapper"
                :class="{
                    'mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8': $store.uiSettings?.contentWidth === 'default',
                    'mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8': $store.uiSettings?.contentWidth === 'wide',
                    'mx-auto max-w-none px-12 py-6': $store.uiSettings?.contentWidth === 'full',
                }"
                class="mx-auto {{ $widthClass }} px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Toast 全局容器 --}}
    <x-ui.toaster />

    @stack('scripts')

    @livewireScripts
</body>
</html>
