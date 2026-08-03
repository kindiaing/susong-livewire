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
    <x-ui.toaster />

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

            {{-- ==================== Input ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Input 输入框</h2>
                <p class="text-sm text-muted-foreground mb-4">支持 label / hint / error / prefix / suffix / disabled / 多种尺寸</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-ui.input label="商品名称" placeholder="请输入商品名称" hint="不超过50个字符" />
                            <x-ui.input label="手机号" type="tel" placeholder="请输入手机号" prefix="+86" />
                            <x-ui.input label="采购价" type="number" placeholder="0.00" suffix="元/斤" />
                            <x-ui.input label="邮箱" type="email" placeholder="admin@example.com" error="邮箱格式不正确" />
                        </div>
                        <div class="flex items-end gap-4">
                            <x-ui.input label="小号" size="sm" placeholder="Small" class="w-40" />
                            <x-ui.input label="默认" placeholder="Default" class="w-40" />
                            <x-ui.input label="大号" size="lg" placeholder="Large" class="w-40" />
                            <x-ui.input label="禁用" placeholder="不可编辑" :disabled="true" class="w-40" />
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Textarea ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Textarea 文本域</h2>
                <p class="text-sm text-muted-foreground mb-4">多行文本输入，支持 label / hint / error</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-ui.textarea label="收货地址" placeholder="请输入详细地址" hint="精确到门牌号" />
                            <x-ui.textarea label="备注" placeholder="请输入备注信息" error="备注内容不能为空" :rows="4" />
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Select ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Select 下拉选择</h2>
                <p class="text-sm text-muted-foreground mb-4">原生 select 组件，支持 label / hint / error / placeholder</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-ui.select label="商品分类" placeholder="请选择分类">
                                <option value="vegetables">蔬菜</option>
                                <option value="fruits">水果</option>
                                <option value="meat">肉禽</option>
                                <option value="seafood">水产</option>
                                <option value="dairy">乳品</option>
                            </x-ui.select>
                            <x-ui.select label="供应商" placeholder="请选择供应商" hint="关联供应商列表" >
                                <option value="1">鲜达生鲜供应</option>
                                <option value="2">绿源果蔬批发</option>
                                <option value="3">海味轩水产</option>
                            </x-ui.select>
                            <x-ui.select label="仓库" error="请选择仓库">
                                <option value="A">仓库 A</option>
                                <option value="B">仓库 B</option>
                                <option value="C">仓库 C</option>
                            </x-ui.select>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Checkbox / Radio / Switch ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Checkbox / Radio / Switch</h2>
                <p class="text-sm text-muted-foreground mb-4">复选框、单选框、开关切换</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {{-- Checkbox --}}
                            <div class="space-y-3">
                                <p class="text-sm font-medium text-foreground">Checkbox 复选框</p>
                                <div class="space-y-2">
                                    <x-ui.checkbox label="蔬菜" checked />
                                    <x-ui.checkbox label="水果" />
                                    <x-ui.checkbox label="肉禽" checked />
                                    <x-ui.checkbox label="乳品（禁用）" disabled />
                                </div>
                            </div>

                            {{-- Radio --}}
                            <div class="space-y-3">
                                <p class="text-sm font-medium text-foreground">Radio 单选框</p>
                                <div class="space-y-2">
                                    <x-ui.radio name="sort" label="按时间" value="time" checked />
                                    <x-ui.radio name="sort" label="按金额" value="amount" />
                                    <x-ui.radio name="sort" label="按名称" value="name" />
                                    <x-ui.radio name="sort" label="按状态" value="status" disabled />
                                </div>
                            </div>

                            {{-- Switch --}}
                            <div class="space-y-3">
                                <p class="text-sm font-medium text-foreground">Switch 开关</p>
                                <div class="space-y-3">
                                    <x-ui.switch label="启用通知" checked />
                                    <x-ui.switch label="自动接单" />
                                    <x-ui.switch label="审核模式" checked />
                                    <x-ui.switch label="维护模式（禁用）" disabled />
                                </div>
                            </div>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Table ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Table 数据表格</h2>
                <p class="text-sm text-muted-foreground mb-4">语义化 HTML 表格，可组合 TableHeader / TableBody / TableRow / TableHead / TableCell</p>
                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>最近采购单</x-ui.card-title>
                        <x-ui.card-description>展示最近 5 条采购记录</x-ui.card-description>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <x-ui.table>
                            <x-ui.table-header>
                                <x-ui.table-row>
                                    <x-ui.table-head>单号</x-ui.table-head>
                                    <x-ui.table-head>供应商</x-ui.table-head>
                                    <x-ui.table-head>金额</x-ui.table-head>
                                    <x-ui.table-head>状态</x-ui.table-head>
                                    <x-ui.table-head class="text-right">操作</x-ui.table-head>
                                </x-ui.table-row>
                            </x-ui.table-header>
                            <x-ui.table-body>
                                <x-ui.table-row>
                                    <x-ui.table-cell class="font-medium">PO-20260727001</x-ui.table-cell>
                                    <x-ui.table-cell>鲜达生鲜供应</x-ui.table-cell>
                                    <x-ui.table-cell>¥12,480.00</x-ui.table-cell>
                                    <x-ui.table-cell><x-ui.badge variant="green">已确认</x-ui.badge></x-ui.table-cell>
                                    <x-ui.table-cell class="text-right"><x-ui.button variant="ghost" size="xs">详情</x-ui.button></x-ui.table-cell>
                                </x-ui.table-row>
                                <x-ui.table-row>
                                    <x-ui.table-cell class="font-medium">PO-20260727002</x-ui.table-cell>
                                    <x-ui.table-cell>绿源果蔬批发</x-ui.table-cell>
                                    <x-ui.table-cell>¥8,320.00</x-ui.table-cell>
                                    <x-ui.table-cell><x-ui.badge variant="outline-orange">待审核</x-ui.badge></x-ui.table-cell>
                                    <x-ui.table-cell class="text-right"><x-ui.button variant="ghost" size="xs">详情</x-ui.button></x-ui.table-cell>
                                </x-ui.table-row>
                                <x-ui.table-row>
                                    <x-ui.table-cell class="font-medium">PO-20260727003</x-ui.table-cell>
                                    <x-ui.table-cell>海味轩水产</x-ui.table-cell>
                                    <x-ui.table-cell>¥25,600.00</x-ui.table-cell>
                                    <x-ui.table-cell><x-ui.badge variant="blue">配送中</x-ui.badge></x-ui.table-cell>
                                    <x-ui.table-cell class="text-right"><x-ui.button variant="ghost" size="xs">详情</x-ui.button></x-ui.table-cell>
                                </x-ui.table-row>
                                <x-ui.table-row>
                                    <x-ui.table-cell class="font-medium">PO-20260726004</x-ui.table-cell>
                                    <x-ui.table-cell>鲜达生鲜供应</x-ui.table-cell>
                                    <x-ui.table-cell>¥6,150.00</x-ui.table-cell>
                                    <x-ui.table-cell><x-ui.badge variant="default">已完成</x-ui.badge></x-ui.table-cell>
                                    <x-ui.table-cell class="text-right"><x-ui.button variant="ghost" size="xs">详情</x-ui.button></x-ui.table-cell>
                                </x-ui.table-row>
                                <x-ui.table-row>
                                    <x-ui.table-cell class="font-medium">PO-20260726005</x-ui.table-cell>
                                    <x-ui.table-cell>绿源果蔬批发</x-ui.table-cell>
                                    <x-ui.table-cell>¥3,280.00</x-ui.table-cell>
                                    <x-ui.table-cell><x-ui.badge variant="red">已取消</x-ui.badge></x-ui.table-cell>
                                    <x-ui.table-cell class="text-right"><x-ui.button variant="ghost" size="xs">详情</x-ui.button></x-ui.table-cell>
                                </x-ui.table-row>
                            </x-ui.table-body>
                            <x-ui.table-footer>
                                <x-ui.table-row>
                                    <x-ui.table-cell colspan="5" class="text-right text-sm text-muted-foreground">
                                        共 5 条记录，合计 ¥55,830.00
                                    </x-ui.table-cell>
                                </x-ui.table-row>
                            </x-ui.table-footer>
                        </x-ui.table>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Pagination ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Pagination 分页</h2>
                <p class="text-sm text-muted-foreground mb-4">页码导航，当前页高亮，支持跳转</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-6">
                        <x-ui.pagination :currentPage="3" :totalPages="10" :total="145" :perPage="15" />
                        <x-ui.pagination :currentPage="1" :totalPages="5" :total="68" :perPage="15" />
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Dialog ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Dialog 对话框</h2>
                <p class="text-sm text-muted-foreground mb-4">模态弹窗，支持 header / title / description / content / footer 组合</p>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.dialog>
                        <x-slot:trigger>
                            <x-ui.button variant="blue">新建采购单</x-ui.button>
                        </x-slot:trigger>

                        <x-ui.dialog-header>
                            <x-ui.dialog-title>新建采购单</x-ui.dialog-title>
                            <x-ui.dialog-description>填写基本信息以创建新的采购单，提交后可继续添加明细。</x-ui.dialog-description>
                        </x-ui.dialog-header>

                        <x-ui.dialog-content>
                            <div class="space-y-4">
                                <x-ui.select label="供应商" placeholder="请选择供应商">
                                    <option value="1">鲜达生鲜供应</option>
                                    <option value="2">绿源果蔬批发</option>
                                </x-ui.select>
                                <x-ui.textarea label="备注" placeholder="可选备注信息" :rows="3" />
                            </div>
                        </x-ui.dialog-content>

                        <x-ui.dialog-footer>
                            <x-ui.button variant="outline">取消</x-ui.button>
                            <x-ui.button variant="blue">提交</x-ui.button>
                        </x-ui.dialog-footer>
                    </x-ui.dialog>

                    <x-ui.dialog size="lg">
                        <x-slot:trigger>
                            <x-ui.button variant="outline">大尺寸对话框</x-ui.button>
                        </x-slot:trigger>

                        <x-ui.dialog-header>
                            <x-ui.dialog-title>大尺寸对话框</x-ui.dialog-title>
                            <x-ui.dialog-description>适用于需要展示更多内容的场景，如明细编辑。</x-ui.dialog-description>
                        </x-ui.dialog-header>

                        <x-ui.dialog-content>
                            <p class="text-sm text-muted-foreground">此对话框使用了 size="lg" 参数，最大宽度为 max-w-2xl，适合编辑表单或显示详细信息。</p>
                        </x-ui.dialog-content>

                        <x-ui.dialog-footer>
                            <x-ui.button variant="outline">关闭</x-ui.button>
                        </x-ui.dialog-footer>
                    </x-ui.dialog>
                </div>
            </section>

            {{-- ==================== Dropdown Menu ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Dropdown Menu 下拉菜单</h2>
                <p class="text-sm text-muted-foreground mb-4">上下文操作菜单，支持分隔线和禁用项</p>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.dropdown-menu>
                        <x-slot:trigger>
                            <x-ui.button variant="outline">更多操作 ▾</x-ui.button>
                        </x-slot:trigger>

                        <x-slot:content>
                            <x-ui.dropdown-item>
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    查看详情
                                </span>
                            </x-ui.dropdown-item>
                            <x-ui.dropdown-item>
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    编辑
                                </span>
                            </x-ui.dropdown-item>
                            <x-ui.dropdown-item disabled>
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    转移（已禁用）
                                </span>
                            </x-ui.dropdown-item>
                            <x-ui.dropdown-separator />
                            <x-ui.dropdown-item variant="destructive">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    删除
                                </span>
                            </x-ui.dropdown-item>
                        </x-slot:content>
                    </x-ui.dropdown-menu>

                    <x-ui.dropdown-menu align="end">
                        <x-slot:trigger>
                            <x-ui.button variant="blue">对齐右 ▾</x-ui.button>
                        </x-slot:trigger>

                        <x-slot:content>
                            <x-ui.dropdown-item>操作一</x-ui.dropdown-item>
                            <x-ui.dropdown-item>操作二</x-ui.dropdown-item>
                            <x-ui.dropdown-item>操作三</x-ui.dropdown-item>
                        </x-slot:content>
                    </x-ui.dropdown-menu>
                </div>
            </section>

            {{-- ==================== Tooltip ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Tooltip 提示</h2>
                <p class="text-sm text-muted-foreground mb-4">鼠标悬停显示简短说明文字</p>
                <div class="flex flex-wrap items-center gap-4">
                    <x-ui.tooltip>
                        <x-ui.button variant="outline">上方提示</x-ui.button>
                        <x-slot:tooltip>这是一个上方提示框</x-slot:tooltip>
                    </x-ui.tooltip>

                    <x-ui.tooltip side="bottom">
                        <x-ui.button variant="outline-blue">下方提示</x-ui.button>
                        <x-slot:tooltip>底部出现的提示</x-slot:tooltip>
                    </x-ui.tooltip>

                    <x-ui.tooltip side="right">
                        <x-ui.button variant="outline-green">右侧提示</x-ui.button>
                        <x-slot:tooltip>右侧显示</x-slot:tooltip>
                    </x-ui.tooltip>
                </div>
            </section>

            {{-- ==================== Tabs ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Tabs 标签页</h2>
                <p class="text-sm text-muted-foreground mb-4">Alpine.js 驱动的标签页切换</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6">
                        <x-ui.tabs defaultTab="orders">
                            <div class="flex border-b border-border mb-4 gap-1">
                                <x-ui.tabs-trigger value="orders">订单管理</x-ui.tabs-trigger>
                                <x-ui.tabs-trigger value="products">商品管理</x-ui.tabs-trigger>
                                <x-ui.tabs-trigger value="drivers">司机管理</x-ui.tabs-trigger>
                                <x-ui.tabs-trigger value="settings">系统设置</x-ui.tabs-trigger>
                            </div>

                            <x-ui.tabs-content value="orders">
                                <div class="space-y-3">
                                    <p class="text-sm text-muted-foreground">订单管理面板 — 查看所有采购、配送订单</p>
                                    <div class="flex gap-2">
                                        <x-ui.button variant="blue" size="sm">新建订单</x-ui.button>
                                        <x-ui.button variant="outline" size="sm">导出</x-ui.button>
                                    </div>
                                </div>
                            </x-ui.tabs-content>

                            <x-ui.tabs-content value="products">
                                <p class="text-sm text-muted-foreground">商品管理面板 — 管理所有商品 SKU、定价和库存</p>
                            </x-ui.tabs-content>

                            <x-ui.tabs-content value="drivers">
                                <p class="text-sm text-muted-foreground">司机管理面板 — 司机排班、线路分配和绩效</p>
                            </x-ui.tabs-content>

                            <x-ui.tabs-content value="settings">
                                <p class="text-sm text-muted-foreground">系统设置面板 — 审核开关、业务参数配置</p>
                            </x-ui.tabs-content>
                        </x-ui.tabs>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Separator ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Separator 分隔线</h2>
                <p class="text-sm text-muted-foreground mb-4">视觉分隔，支持水平 / 垂直方向</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">上方内容</p>
                        </div>
                        <x-ui.separator />
                        <div>
                            <p class="text-sm text-muted-foreground">下方内容</p>
                        </div>
                        <div class="flex items-center gap-4 h-8">
                            <span class="text-sm">选项 A</span>
                            <x-ui.separator orientation="vertical" class="h-4" />
                            <span class="text-sm">选项 B</span>
                            <x-ui.separator orientation="vertical" class="h-4" />
                            <span class="text-sm">选项 C</span>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Spinner / Skeleton ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Spinner / Skeleton 加载态</h2>
                <p class="text-sm text-muted-foreground mb-4">加载指示器和骨架屏占位</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-6">
                        <div>
                            <p class="text-sm font-medium mb-3">Spinner 旋转加载</p>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <x-ui.spinner size="sm" variant="primary" />
                                    <span class="text-sm text-muted-foreground">小号</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-ui.spinner variant="primary" />
                                    <span class="text-sm text-muted-foreground">默认</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-ui.spinner size="lg" variant="primary" />
                                    <span class="text-sm text-muted-foreground">大号</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-ui.spinner variant="muted" />
                                    <span class="text-sm text-muted-foreground">灰色</span>
                                </div>
                            </div>
                        </div>

                        <x-ui.separator />

                        <div>
                            <p class="text-sm font-medium mb-3">Skeleton 骨架屏</p>
                            <div class="space-y-3">
                                <x-ui.skeleton class="h-4 w-[250px]" />
                                <x-ui.skeleton class="h-4 w-[200px]" />
                                <x-ui.skeleton class="h-12 w-full" variant="circular" />
                            </div>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Avatar ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Avatar 头像</h2>
                <p class="text-sm text-muted-foreground mb-4">用户头像，支持图片 / 首字母 / 占位，多种尺寸</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6">
                        <div class="flex items-center gap-4">
                            <x-ui.avatar size="xs" fallback="张三" />
                            <x-ui.avatar size="sm" fallback="李四" />
                            <x-ui.avatar size="default" fallback="王五" />
                            <x-ui.avatar size="lg" fallback="赵六" />
                            <x-ui.avatar size="xl" />
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- ==================== Progress ==================== --}}
            <section>
                <h2 class="text-lg font-semibold mb-1">Progress 进度条</h2>
                <p class="text-sm text-muted-foreground mb-4">彩色进度条，支持多种颜色和尺寸</p>
                <x-ui.card>
                    <x-ui.card-content class="p-6 space-y-5">
                        <div class="space-y-3">
                            <p class="text-sm font-medium">配送完成率</p>
                            <x-ui.progress :value="78" :max="100" variant="blue" showValue />
                        </div>
                        <div class="space-y-3">
                            <p class="text-sm font-medium">库存达标率</p>
                            <x-ui.progress :value="45" :max="100" variant="green" showValue />
                        </div>
                        <div class="space-y-3">
                            <p class="text-sm font-medium">审核积压率</p>
                            <x-ui.progress :value="92" :max="100" variant="orange" showValue />
                        </div>
                        <div class="space-y-3">
                            <p class="text-sm font-medium">退货率</p>
                            <x-ui.progress :value="12" :max="100" variant="red" showValue />
                        </div>
                        <div class="space-y-3">
                            <p class="text-sm font-medium">默认样式</p>
                            <x-ui.progress :value="60" :max="100" showValue />
                        </div>
                        <div class="space-y-3">
                            <p class="text-sm font-medium">不同尺寸</p>
                            <x-ui.progress :value="55" :max="100" variant="blue" size="sm" />
                            <x-ui.progress :value="55" :max="100" variant="blue" size="default" />
                            <x-ui.progress :value="55" :max="100" variant="blue" size="lg" />
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
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
