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
            $icpNumber = \App\Support\Setting::get('site_icp_number', '');
            $icpUrl = \App\Support\Setting::get('site_icp_url', 'https://beian.miit.gov.cn/');
            $techStackUrl = \App\Support\Setting::get('site_tech_stack_url', 'https://laravel.com');
            $developerName = \App\Support\Setting::get('site_developer_name', 'Seeding');
            $developerUrl = \App\Support\Setting::get('site_developer_url', '');
        } catch (\Throwable) {
            $uiCloseOnOutside = true;
            $icpNumber = '';
            $icpUrl = 'https://beian.miit.gov.cn/';
            $techStackUrl = 'https://laravel.com';
            $developerName = 'Seeding';
            $developerUrl = '';
        }
    @endphp
    <script>
        window.__UI_CLOSE_ON_OUTSIDE = {{ $uiCloseOnOutside ? 'true' : 'false' }};
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

        {{-- 底部版权栏（始终显示） --}}
        <footer class="border-t bg-background/95">
            <div class="px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center gap-2 sm:flex-row sm:justify-center sm:gap-4 text-xs text-muted-foreground">
                    {{-- 技术栈 --}}
                    <span>
                        Powered by
                        <a href="{{ $techStackUrl }}" target="_blank" rel="noopener noreferrer" class="font-medium text-foreground/80 hover:text-primary transition-colors">Laravel</a> +
                        <a href="{{ $techStackUrl }}" target="_blank" rel="noopener noreferrer" class="font-medium text-foreground/80 hover:text-primary transition-colors">Livewire 4</a> +
                        <a href="{{ $techStackUrl }}" target="_blank" rel="noopener noreferrer" class="font-medium text-foreground/80 hover:text-primary transition-colors">Alpine.js</a> +
                        <a href="{{ $techStackUrl }}" target="_blank" rel="noopener noreferrer" class="font-medium text-foreground/80 hover:text-primary transition-colors">Tailwind CSS</a>
                    </span>

                    <span class="hidden sm:inline text-border">|</span>

                    {{-- 开发者 --}}
                    @if($developerName)
                        <span>
                            Developed by
                            @if($developerUrl)
                                <a href="{{ $developerUrl }}" target="_blank" rel="noopener noreferrer" class="font-medium text-foreground/80 hover:text-primary transition-colors">{{ $developerName }}</a>
                            @else
                                <span class="font-medium text-foreground/80">{{ $developerName }}</span>
                            @endif
                        </span>
                    @endif

                    {{-- 备案号 --}}
                    <span class="hidden sm:inline text-border">|</span>
                    @if($icpNumber)
                        <a href="{{ $icpUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">{{ $icpNumber }}</a>
                    @else
                        <a href="{{ $icpUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">暂未备案</a>
                    @endif
                </div>
            </div>
        </footer>
    </div>

    {{-- Toast 全局容器 --}}
    <x-ui.toaster />

    @livewireScripts
</body>
</html>
