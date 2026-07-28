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
    @php
        try {
            $uiCloseOnOutside = \App\Support\Setting::get('ui_close_on_outside', true);
            $uiShowFooter = \App\Support\Setting::get('ui_show_footer', true);
            $icpNumber = \App\Support\Setting::get('site_icp_number', '');
        } catch (\Throwable) {
            $uiCloseOnOutside = true;
            $uiShowFooter = true;
            $icpNumber = '';
        }
    @endphp
    <script>
        window.__UI_CLOSE_ON_OUTSIDE = {{ $uiCloseOnOutside ? 'true' : 'false' }};
        window.__UI_SHOW_FOOTER = {{ $uiShowFooter ? 'true' : 'false' }};
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

        {{-- 底部版权栏（Alpine 响应式控制，保存后即时生效） --}}
        <footer x-data x-show="$store.uiSettings.showFooter" class="border-t bg-background/95">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center gap-1 sm:flex-row sm:justify-between">
                    <div class="text-xs text-muted-foreground">
                        &copy; {{ date('Y') }} {{ config('app.name', '本地速送') }} &middot;
                        Powered by <span class="font-medium text-foreground/80">Laravel {{ app()->version() }}</span> +
                        <span class="font-medium text-foreground/80">Livewire 4</span> +
                        <span class="font-medium text-foreground/80">Alpine.js</span> +
                        <span class="font-medium text-foreground/80">Tailwind CSS</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-muted-foreground">
                        @if($icpNumber)
                            <span>{{ $icpNumber }}</span>
                        @endif
                        <span>Developed by <span class="font-medium text-foreground/80">Seeding</span></span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- Toast 全局容器 --}}
    <x-ui.toaster />

    @livewireScripts
</body>
</html>
