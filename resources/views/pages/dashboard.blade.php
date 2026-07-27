<x-app-layout>
    <div class="p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-foreground">仪表盘</h1>
            <p class="text-muted-foreground mt-1">欢迎回来，{{ auth()->user()->name }}</p>
        </div>

        <!-- 统计卡片 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-ui.card>
                <x-ui.card-content class="p-6">
                    <p class="text-sm text-muted-foreground">今日订单</p>
                    <p class="text-3xl font-bold mt-2">0</p>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card>
                <x-ui.card-content class="p-6">
                    <p class="text-sm text-muted-foreground">待处理审核</p>
                    <p class="text-3xl font-bold mt-2">0</p>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card>
                <x-ui.card-content class="p-6">
                    <p class="text-sm text-muted-foreground">今日采购单</p>
                    <p class="text-3xl font-bold mt-2">0</p>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card>
                <x-ui.card-content class="p-6">
                    <p class="text-sm text-muted-foreground">库存预警</p>
                    <p class="text-3xl font-bold mt-2">0</p>
                </x-ui.card-content>
            </x-ui.card>
        </div>

        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>系统信息</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content class="p-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-muted-foreground">系统版本：</span>
                        <span>V1.0.0</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground">技术栈：</span>
                        <span>Laravel + Livewire 4</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground">PHP 版本：</span>
                        <span>{{ PHP_VERSION }}</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground">Laravel 版本：</span>
                        <span>{{ app()->version() }}</span>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>
