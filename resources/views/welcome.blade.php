<!DOCTYPE html>
<html lang="zh-CN" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', '本地速送') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground min-h-screen font-sans flex flex-col">
    {{-- 顶部栏 --}}
    <header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="flex h-14 items-center px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <a href="/" class="flex items-center gap-2 mr-auto">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0H21M3.375 14.25h.008v.008h-.008v-.008Zm0 0H7.5m0 0v.375c0 .621.504 1.125 1.125 1.125h6.75c.621 0 1.125-.504 1.125-1.125V14.25m-9 0H3.375m0 0V6.375c0-.621.504-1.125 1.125-1.125h6.75c.621 0 1.125.504 1.125 1.125v7.875m9 0v.375a1.5 1.5 0 0 1-1.5 1.5H15M3.375 14.25h6.75"/>
                    </svg>
                </div>
                <span class="font-semibold text-lg">{{ config('app.name', '本地速送') }}</span>
            </a>

            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center rounded-sm bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
                登录
            </a>
        </div>
    </header>

    {{-- 主内容 --}}
    <main class="flex-1 flex items-center justify-center">
        <div class="mx-auto max-w-3xl px-6 py-16 text-center">
            {{-- Logo --}}
            <div class="flex justify-center mb-8">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0H21M3.375 14.25h.008v.008h-.008v-.008Zm0 0H7.5m0 0v.375c0 .621.504 1.125 1.125 1.125h6.75c.621 0 1.125-.504 1.125-1.125V14.25m-9 0H3.375m0 0V6.375c0-.621.504-1.125 1.125-1.125h6.75c.621 0 1.125.504 1.125 1.125v7.875m9 0v.375a1.5 1.5 0 0 1-1.5 1.5H15M3.375 14.25h6.75"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl">
                {{ config('app.name', '本地速送') }}
            </h1>
            <p class="mt-4 text-lg text-muted-foreground leading-8">
                高效的本地速送服务平台，支持生鲜配送、库存管理、订单配送、财务结算全流程管理
            </p>

            <div class="mt-8 flex items-center justify-center gap-4">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-sm bg-primary px-6 py-3 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors shadow-sm">
                    登录管理后台
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
            </div>

            {{-- 技术栈标签 --}}
            <div class="mt-12 flex flex-wrap items-center justify-center gap-3 text-xs text-muted-foreground">
                <span class="inline-flex items-center rounded-full border border-border px-3 py-1">Laravel {{ app()->version() }}</span>
                <span class="inline-flex items-center rounded-full border border-border px-3 py-1">Livewire 4</span>
                <span class="inline-flex items-center rounded-full border border-border px-3 py-1">Alpine.js</span>
                <span class="inline-flex items-center rounded-full border border-border px-3 py-1">Tailwind CSS</span>
            </div>
        </div>
    </main>

    {{-- 底部备案号 --}}
    @php
        try {
            $icpNumber = \App\Support\Setting::get('site_icp_number', '');
        } catch (\Throwable) {
            $icpNumber = '';
        }
    @endphp
    <footer class="border-t bg-background/95">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8 text-center text-xs text-muted-foreground">
            @if($icpNumber)
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer" class="hover:text-foreground transition-colors">{{ $icpNumber }}</a>
            @endif
        </div>
    </footer>
</body>
</html>
