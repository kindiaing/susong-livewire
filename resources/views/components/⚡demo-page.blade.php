<?php

use Livewire\Component;

new class extends Component
{
    public $clickCount = 0;

    public function increment()
    {
        $this->clickCount++;
    }

    public function resetCount()
    {
        $this->clickCount = 0;
    }
};
?>

<div>
    <!-- Toast container (global) -->
    <x-ui.toast />

    <div class="min-h-screen bg-muted/30">
        <!-- Header -->
        <header class="border-b border-border bg-background sticky top-0 z-40">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-foreground">UI 组件库 Demo</h1>
                        <p class="text-sm text-muted-foreground mt-0.5">shadcn/ui 风格 · Tailwind CSS 4 · Blade 组件</p>
                    </div>
                    <x-ui.badge variant="blue">v1.0</x-ui.badge>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-10">

            {{-- ==================== Breadcrumb ==================== --}}
            <section>
                <x-ui.breadcrumb>
                    <x-ui.breadcrumb-item href="/">首页</x-ui.breadcrumb-item>
                    <svg class="w-3.5 h-3.5 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <x-ui.breadcrumb-item href="/demo">组件库</x-ui.breadcrumb-item>
                    <svg class="w-3.5 h-3.5 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <x-ui.breadcrumb-item :active="true">全部展示</x-ui.breadcrumb-item>
                </x-ui.breadcrumb>
            </section>

            {{-- ==================== Button ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Button 按钮</h2>
                <p class="text-sm text-muted-foreground mb-4">多色按钮变体，支持 default / blue / green / orange / red / yellow / purple / outline / ghost / link</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-6">
                        {{-- Solid Buttons --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button>Default</x-ui.button>
                            <x-ui.button variant="blue">Blue</x-ui.button>
                            <x-ui.button variant="green">Green</x-ui.button>
                            <x-ui.button variant="orange">Orange</x-ui.button>
                            <x-ui.button variant="red">Red</x-ui.button>
                            <x-ui.button variant="yellow">Yellow</x-ui.button>
                            <x-ui.button variant="purple">Purple</x-ui.button>
                        </div>
                        {{-- Outline Buttons --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button variant="outline">Outline</x-ui.button>
                            <x-ui.button variant="outline-blue">Blue</x-ui.button>
                            <x-ui.button variant="outline-green">Green</x-ui.button>
                            <x-ui.button variant="outline-orange">Orange</x-ui.button>
                            <x-ui.button variant="outline-red">Red</x-ui.button>
                        </div>
                        {{-- Ghost / Link / Sizes --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button variant="ghost">Ghost</x-ui.button>
                            <x-ui.button variant="link">Link</x-ui.button>
                            <x-ui.button variant="secondary">Secondary</x-ui.button>
                            <x-ui.button variant="blue" size="xs">XS</x-ui.button>
                            <x-ui.button variant="blue" size="sm">SM</x-ui.button>
                            <x-ui.button variant="blue" size="default">Default</x-ui.button>
                            <x-ui.button variant="blue" size="lg">LG</x-ui.button>
                        </div>
                        {{-- Disabled --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button variant="blue" :disabled="true">Disabled</x-ui.button>
                            <x-ui.button variant="green" :disabled="true">Disabled</x-ui.button>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Badge ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Badge 徽标</h2>
                <p class="text-sm text-muted-foreground mb-4">用于状态标签、分类标记，支持 solid / outline 双风格</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.badge>Default</x-ui.badge>
                            <x-ui.badge variant="blue">Blue</x-ui.badge>
                            <x-ui.badge variant="green">Green</x-ui.badge>
                            <x-ui.badge variant="orange">Orange</x-ui.badge>
                            <x-ui.badge variant="red">Red</x-ui.badge>
                            <x-ui.badge variant="yellow">Yellow</x-ui.badge>
                            <x-ui.badge variant="purple">Purple</x-ui.badge>
                            <x-ui.badge variant="secondary">Secondary</x-ui.badge>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.badge variant="outline">Outline</x-ui.badge>
                            <x-ui.badge variant="outline-blue">Blue</x-ui.badge>
                            <x-ui.badge variant="outline-green">Green</x-ui.badge>
                            <x-ui.badge variant="outline-orange">Orange</x-ui.badge>
                            <x-ui.badge variant="outline-red">Red</x-ui.badge>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Alert ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Alert 提示</h2>
                <p class="text-sm text-muted-foreground mb-4">信息 / 成功 / 警告 / 危险四种风格，左侧彩色边框高亮</p>
                <div class="space-y-4">
                    <x-ui.alert variant="info">
                        <strong>信息提示</strong> — 系统将于今晚 23:00 进行例行维护，预计 30 分钟完成。
                    </x-ui.alert>
                    <x-ui.alert variant="success">
                        <strong>操作成功</strong> — 采购单已成功提交，等待供应商确认。
                    </x-ui.alert>
                    <x-ui.alert variant="warning">
                        <strong>注意</strong> — 当前库存低于预警值，请及时补货。
                    </x-ui.alert>
                    <x-ui.alert variant="destructive">
                        <strong>操作失败</strong> — 付款金额超出账户余额，请充值后重试。
                    </x-ui.alert>
                    <x-ui.alert variant="purple">
                        <strong>新功能</strong> — 拖拽排序看板已上线，前往「订单管理」体验。
                    </x-ui.alert>
                </div>
            </section>

            {{-- ==================== Card ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Card 卡片</h2>
                <p class="text-sm text-muted-foreground mb-4">可组合 CardHeader + CardTitle + CardDescription + CardContent + CardFooter</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-ui.card>
                        <x-ui.card-header>
                            <x-ui.card-title>今日订单</x-ui.card-title>
                            <x-ui.card-description>今日已完成订单数量</x-ui.card-description>
                        </x-ui.card-header>
                        <x-ui.card-content>
                            <p class="text-3xl font-bold text-blue-600">1,284</p>
                            <p class="text-xs text-muted-foreground mt-1">较昨日 <span class="text-green-600 font-medium">+12.5%</span></p>
                        </x-ui.card-content>
                        <x-ui.card-footer>
                            <x-ui.button variant="outline-blue" size="sm">查看详情</x-ui.button>
                        </x-ui.card-footer>
                    </x-ui.card>

                    <x-ui.card>
                        <x-ui.card-header>
                            <x-ui.card-title>待结算金额</x-ui.card-title>
                            <x-ui.card-description>采购结算单汇总</x-ui.card-description>
                        </x-ui.card-header>
                        <x-ui.card-content>
                            <p class="text-3xl font-bold text-orange-600">¥52,380</p>
                            <p class="text-xs text-muted-foreground mt-1"><span class="text-orange-600 font-medium">3 笔</span> 待审核</p>
                        </x-ui.card-content>
                        <x-ui.card-footer>
                            <x-ui.button variant="outline-orange" size="sm">去结算</x-ui.button>
                        </x-ui.card-footer>
                    </x-ui.card>

                    <x-ui.card>
                        <x-ui.card-header>
                            <x-ui.card-title>库存预警</x-ui.card-title>
                            <x-ui.card-description>低于预警值的 SKU</x-ui.card-description>
                        </x-ui.card-header>
                        <x-ui.card-content>
                            <p class="text-3xl font-bold text-red-600">17</p>
                            <p class="text-xs text-muted-foreground mt-1">需紧急补货 <span class="text-red-600 font-medium">5 项</span></p>
                        </x-ui.card-content>
                        <x-ui.card-footer>
                            <x-ui.button variant="outline-red" size="sm">处理预警</x-ui.button>
                        </x-ui.card-footer>
                    </x-ui.card>
                </div>
            </section>

            {{-- ==================== AlertDialog ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">AlertDialog 确认弹窗</h2>
                <p class="text-sm text-muted-foreground mb-4">模态确认框，防止误操作，支持 destructive / warning / info 风格</p>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.alert-dialog title="确认删除？" description="此操作将永久删除该采购单及所有关联明细，无法恢复。" confirmText="确认删除" variant="destructive">
                        <x-ui.button variant="red">删除采购单</x-ui.button>
                    </x-ui.alert-dialog>

                    <x-ui.alert-dialog title="提交审核？" description="提交后订单将进入审核流程，审核通过前不可修改。" confirmText="提交" variant="info">
                        <x-ui.button variant="blue">提交审核</x-ui.button>
                    </x-ui.alert-dialog>

                    <x-ui.alert-dialog title="确认取消？" description="取消后已填写的表单数据将丢失。" confirmText="确认取消" variant="warning">
                        <x-ui.button variant="orange">取消订单</x-ui.button>
                    </x-ui.alert-dialog>
                </div>
            </section>

            {{-- ==================== Drawer ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Drawer 抽屉</h2>
                <p class="text-sm text-muted-foreground mb-4">侧滑面板，支持上下左右四个方向，常用于详情查看、筛选配置</p>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.drawer title="订单详情" position="right" width="420px">
                        <x-ui.button variant="blue">右侧抽屉</x-ui.button>
                        <x-slot:drawerContent>
                            <div class="space-y-4">
                                <div class="rounded-md bg-blue-50 p-3">
                                    <p class="text-sm font-medium text-blue-600">订单 #20260727001</p>
                                    <p class="text-xs text-blue-600/70 mt-1">创建于 2026-07-27 10:30</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm"><span class="text-muted-foreground">商家</span><span class="font-medium">鲜达生鲜</span></div>
                                    <div class="flex justify-between text-sm"><span class="text-muted-foreground">金额</span><span class="font-medium text-blue-600">¥3,840.00</span></div>
                                    <div class="flex justify-between text-sm"><span class="text-muted-foreground">状态</span><x-ui.badge variant="outline-green">已确认</x-ui.badge></div>
                                    <div class="flex justify-between text-sm"><span class="text-muted-foreground">配送</span><span class="font-medium">线路 A · 司机张三</span></div>
                                </div>
                            </div>
                        </x-slot:drawerContent>
                    </x-ui.drawer>

                    <x-ui.drawer title="筛选条件" position="left" width="360px">
                        <x-ui.button variant="green">左侧抽屉</x-ui.button>
                        <x-slot:drawerContent>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium">日期范围</label>
                                    <div class="mt-2 grid grid-cols-2 gap-2">
                                        <div class="rounded-md border border-input px-3 py-2 text-sm text-muted-foreground">开始日期</div>
                                        <div class="rounded-md border border-input px-3 py-2 text-sm text-muted-foreground">结束日期</div>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">订单状态</label>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <x-ui.badge variant="blue">全部</x-ui.badge>
                                        <x-ui.badge variant="outline">待确认</x-ui.badge>
                                        <x-ui.badge variant="outline">已确认</x-ui.badge>
                                        <x-ui.badge variant="outline">配送中</x-ui.badge>
                                        <x-ui.badge variant="outline">已完成</x-ui.badge>
                                    </div>
                                </div>
                            </div>
                        </x-slot:drawerContent>
                    </x-ui.drawer>

                    <x-ui.drawer title="顶部通知" position="top" width="100%">
                        <x-ui.button variant="orange">顶部抽屉</x-ui.button>
                        <x-slot:drawerContent>
                            <p class="text-sm">这是一个从顶部滑入的抽屉面板，适合展示全宽通知或工具栏。</p>
                        </x-slot:drawerContent>
                    </x-ui.drawer>

                    <x-ui.drawer title="底部面板" position="bottom" width="300px">
                        <x-ui.button variant="purple">底部抽屉</x-ui.button>
                        <x-slot:drawerContent>
                            <p class="text-sm">从底部滑入的面板，适合移动端交互场景。</p>
                        </x-slot:drawerContent>
                    </x-ui.drawer>
                </div>
            </section>

            {{-- ==================== HoverCard ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">HoverCard 悬浮卡片</h2>
                <p class="text-sm text-muted-foreground mb-4">鼠标悬停显示详情面板，适合用户卡片、商品快览</p>
                <div class="flex flex-wrap items-center gap-6">
                    <x-ui.hover-card>
                        <div class="flex items-center gap-3 rounded-lg border border-border p-3 cursor-pointer hover:border-blue-600/50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">张</div>
                            <div>
                                <p class="text-sm font-medium">张三</p>
                                <p class="text-xs text-muted-foreground">配送司机 · 线路 A</p>
                            </div>
                        </div>
                        <x-slot:hoverContent>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">张</div>
                                    <div>
                                        <p class="font-semibold">张三</p>
                                        <p class="text-xs text-muted-foreground">司驾编号 D-1001</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div><span class="text-muted-foreground">今日配送</span><p class="font-medium text-green-600">23 单</p></div>
                                    <div><span class="text-muted-foreground">本月配送</span><p class="font-medium">486 单</p></div>
                                    <div><span class="text-muted-foreground">评分</span><p class="font-medium text-orange-600">4.9 / 5</p></div>
                                    <div><span class="text-muted-foreground">状态</span><p class="font-medium text-green-600">在线</p></div>
                                </div>
                            </div>
                        </x-slot:hoverContent>
                    </x-ui.hover-card>

                    <x-ui.hover-card>
                        <div class="flex items-center gap-3 rounded-lg border border-border p-3 cursor-pointer hover:border-green-600/50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white text-sm">SKU</div>
                            <div>
                                <p class="text-sm font-medium">西红柿 500g</p>
                                <p class="text-xs text-muted-foreground">采购参考价 ¥3.20</p>
                            </div>
                        </div>
                        <x-slot:hoverContent>
                            <div class="space-y-2">
                                <p class="font-semibold">西红柿 500g</p>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div><span class="text-muted-foreground">批发价</span><p class="font-medium text-blue-600">¥4.5000</p></div>
                                    <div><span class="text-muted-foreground">成本价</span><p class="font-medium text-orange-600">¥3.2000</p></div>
                                    <div><span class="text-muted-foreground">库存</span><p class="font-medium">2,400</p></div>
                                    <div><span class="text-muted-foreground">状态</span><p class="font-medium text-green-600">上架</p></div>
                                </div>
                            </div>
                        </x-slot:hoverContent>
                    </x-ui.hover-card>
                </div>
            </section>

            {{-- ==================== Toast (Sonner) ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Toast 轻提醒</h2>
                <p class="text-sm text-muted-foreground mb-4">Alpine.js 驱动，支持 info / success / warning / destructive 四种类型，4 秒自动消失</p>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button variant="blue"
                        x-on:click="window.dispatchEvent(new CustomEvent('toast:show', { detail: { title: '信息提示', description: '系统消息已更新', variant: 'info' } }))">
                        Info Toast
                    </x-ui.button>
                    <x-ui.button variant="green"
                        x-on:click="window.dispatchEvent(new CustomEvent('toast:show', { detail: { title: '操作成功', description: '采购单已保存为草稿', variant: 'success' } }))">
                        Success Toast
                    </x-ui.button>
                    <x-ui.button variant="orange"
                        x-on:click="window.dispatchEvent(new CustomEvent('toast:show', { detail: { title: '注意', description: '库存不足，请及时补货', variant: 'warning' } }))">
                        Warning Toast
                    </x-ui.button>
                    <x-ui.button variant="red"
                        x-on:click="window.dispatchEvent(new CustomEvent('toast:show', { detail: { title: '操作失败', description: '网络异常，请稍后重试', variant: 'destructive' } }))">
                        Destructive Toast
                    </x-ui.button>
                </div>
            </section>

            {{-- ==================== Livewire 交互 ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Livewire 交互演示</h2>
                <p class="text-sm text-muted-foreground mb-4">Livewire 服务端状态驱动，无需手写 JS 即可完成交互</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6">
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-sm text-muted-foreground">点击计数</p>
                                <p class="text-4xl font-bold text-blue-600 mt-1">{{ $clickCount }}</p>
                            </div>
                            <div class="flex gap-3">
                                <x-ui.button variant="green" wire:click="increment">
                                    +1 增加
                                </x-ui.button>
                                <x-ui.button variant="outline" wire:click="resetCount">
                                    重置
                                </x-ui.button>
                            </div>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Stat Blocks (数据看板) ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Stat Block 数据看板</h2>
                <p class="text-sm text-muted-foreground mb-4">常用业务统计卡片，彩色图标 + 数字 + 趋势标签</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-ui.card>
                        <x-ui.card-content class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-muted-foreground">今日营收</p>
                                    <p class="text-2xl font-bold text-green-600 mt-1">¥128,400</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground mt-2"><span class="text-green-600 font-medium">+8.2%</span> 较昨日</p>
                        </x-ui.card-content>
                    </x-ui.card>

                    <x-ui.card>
                        <x-ui.card-content class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-muted-foreground">配送中</p>
                                    <p class="text-2xl font-bold text-blue-600 mt-1">36</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground mt-2"><span class="text-blue-600 font-medium">5 位</span>司机在线</p>
                        </x-ui.card-content>
                    </x-ui.card>

                    <x-ui.card>
                        <x-ui.card-content class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-muted-foreground">待审核</p>
                                    <p class="text-2xl font-bold text-orange-600 mt-1">8</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground mt-2"><span class="text-orange-600 font-medium">2 笔</span>超 24h</p>
                        </x-ui.card-content>
                    </x-ui.card>

                    <x-ui.card>
                        <x-ui.card-content class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-muted-foreground">库存预警</p>
                                    <p class="text-2xl font-bold text-red-600 mt-1">12</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground mt-2"><span class="text-red-600 font-medium">5 项</span>紧急补货</p>
                        </x-ui.card-content>
                    </x-ui.card>
                </div>
            </section>

            {{-- ==================== Status Badges 组合 ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">业务状态组合</h2>
                <p class="text-sm text-muted-foreground mb-4">典型业务场景中的状态标签搭配</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-4">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">订单状态:</span>
                                <x-ui.badge variant="blue">待确认</x-ui.badge>
                                <x-ui.badge variant="green">已确认</x-ui.badge>
                                <x-ui.badge variant="orange">配送中</x-ui.badge>
                                <x-ui.badge variant="default">已完成</x-ui.badge>
                                <x-ui.badge variant="red">已取消</x-ui.badge>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">审核状态:</span>
                                <x-ui.badge variant="outline-orange">待审核</x-ui.badge>
                                <x-ui.badge variant="outline-green">已通过</x-ui.badge>
                                <x-ui.badge variant="outline-red">已拒绝</x-ui.badge>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">结算状态:</span>
                                <x-ui.badge variant="orange">待付款</x-ui.badge>
                                <x-ui.badge variant="blue">部分付款</x-ui.badge>
                                <x-ui.badge variant="green">已结清</x-ui.badge>
                            </div>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

        </main>

        <!-- Footer -->
        <footer class="border-t border-border bg-background mt-12">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 text-center text-xs text-muted-foreground">
                本地速送服务平台 · shadcn/ui 风格组件库 · Laravel 13 + Livewire 4 + Tailwind CSS 4
            </div>
        </footer>
    </div>
</div>
