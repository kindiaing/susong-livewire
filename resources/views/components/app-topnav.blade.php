@props([])
{{--
  顶部导航栏组件
  布局：整体居中 max-w-7xl，Logo 靠左，右侧功能区靠右，导航+搜索居中
--}}
<header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
    <div class="flex h-14 items-center px-4 max-w-7xl mx-auto w-full">

        {{-- 左侧：Logo + 系统名称（靠近左端） --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0 mr-6">
            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
                <x-ui.icon name="truck-delivery" class="w-5 h-5" />
            </div>
            <span class="font-semibold text-lg hidden sm:inline">本地速送</span>
        </a>

        {{-- 中间：主导航 + Command 搜索（整体居中） --}}
        <div class="flex-1 flex items-center justify-center gap-4">
            {{-- 主航 --}}
            <x-nav-menu class="hidden md:flex">

                {{-- 商品管理 --}}
                <x-nav-menu-item value="product">
                    <x-nav-menu-trigger icon="cube">商品管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="product">
                        <x-nav-menu-icon-link href="#" icon="swatch">分类管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="cube">商品管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="tag">SKU 管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="eye">可见性配置</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="hashtag">关键词标签</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="qr-code">条码管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="squares">一品多供</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 采购管理 --}}
                <x-nav-menu-item value="purchase">
                    <x-nav-menu-trigger icon="cart">采购管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="purchase">
                        <x-nav-menu-link href="#" description="自动汇总待采商品，一键生成采购单">待采清单</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="采购单全流程管理：创建→接单→发货→入库→完成">采购单管理</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="退货审批、库存联动扣减、应付扣减">采购退货</x-nav-menu-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 订单配送 --}}
                <x-nav-menu-item value="order">
                    <x-nav-menu-trigger icon="clipboard">订单配送</x-nav-menu-trigger>
                    <x-nav-menu-content value="order">
                        <x-nav-menu-icon-link href="#" icon="lock-closed">客户订单</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="shopping-bag">购物车</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="truck">配送任务</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="check-badge">签收存证</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="exclamation-triangle">差异处理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="arrow-uturn-left">售后退货</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 库存拣货 --}}
                <x-nav-menu-item value="inventory">
                    <x-nav-menu-trigger icon="chart-bar">库存拣货</x-nav-menu-trigger>
                    <x-nav-menu-content value="inventory">
                        <x-nav-menu-link href="#" description="仓库CRUD、类型（总仓/前置仓）、冷链标记">仓库管理</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="按仓库+SKU维度展示，总/锁定/可用库存">实时库存</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="全量变动记录，5种变动类型筛选">库存日志</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="预警列表、通知运营人员">库存预警</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="拣货任务创建/分配/执行">拣货任务</x-nav-menu-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 财务管理 --}}
                <x-nav-menu-item value="finance">
                    <x-nav-menu-trigger icon="banknotes">财务管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="finance">
                        <x-nav-menu-icon-link href="#" icon="wallet">客户账户</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="bank">客户充值</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="briefcase">供应商结算</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="banknotes">应收账款</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="document">发票管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="pencil">授权更正</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="shield">价格策略</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="pie-chart">费用均摊</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="trash">损耗管理</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 用户权限 --}}
                <x-nav-menu-item value="user">
                    <x-nav-menu-trigger icon="users">用户权限</x-nav-menu-trigger>
                    <x-nav-menu-content value="user">
                        <x-nav-menu-link href="#" description="用户CRUD、重置密码、禁用/启用">用户管理</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="9个系统角色、权限配置（树形结构）">角色管理</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="权限树形列表、模块/页面/按钮级控制">权限管理</x-nav-menu-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 组织主体 --}}
                <x-nav-menu-item value="org">
                    <x-nav-menu-trigger icon="building">组织主体</x-nav-menu-trigger>
                    <x-nav-menu-content value="org">
                        <x-nav-menu-icon-link href="#" icon="building-office">供应商管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="store">商家管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="signal">配送线路</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="user-circle">司机管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="truck">车辆管理</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 系统管理 --}}
                <x-nav-menu-item value="system">
                    <x-nav-menu-trigger icon="cog">系统管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="system">
                        <x-nav-menu-link href="{{ route('settings') }}" description="6组17项系统配置，分组导航+列表编辑">系统配置</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="首页轮播广告管理">轮播广告</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="主推商品/品类配置">运营主推</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="按操作人/时间/模块筛选">操作日志</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="敏感操作审计，保留策略0-180天">审计日志</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="19个审核节点开关、审核列表">审核管理</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="改价记录列表/详情、数据快照回溯">改价记录</x-nav-menu-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

            </x-nav-menu>

            {{-- Command 搜索 --}}
            <div class="hidden md:block">
                <x-ui.command />
            </div>
        </div>

        {{-- 移动端：汉堡菜单 + Command 搜索 --}}
        <div class="flex md:hidden items-center gap-1">
            <x-ui.command />
            <button class="p-2 rounded-md hover:bg-accent hover:text-accent-foreground"
                    x-data="{ mobileOpen: false }"
                    @click="mobileOpen = !mobileOpen">
                <x-ui.icon name="bars" class="w-5 h-5" />
            </button>
        </div>

        {{-- 右侧功能区（靠近右端） --}}
        <div x-data class="flex items-center gap-1 shrink-0 ml-4">

            {{-- 通知 Drawer --}}
            <div x-data="notificationDrawer()" x-init="init()">
                {{-- 通知铃铛按钮 --}}
                <button type="button" @click="open = !open"
                        class="relative p-2 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors"
                        title="通知">
                    <x-ui.icon name="bell" class="w-5 h-5" />
                    {{-- 未读角标 --}}
                    <template x-if="unreadCount > 0">
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-destructive text-[10px] font-medium text-destructive-foreground"
                              x-text="unreadCount"></span>
                    </template>
                </button>

                {{-- 通知 Drawer 面板（x-teleport 到 body） --}}
                <template x-teleport="body">
                    <div x-show="open" class="fixed inset-0 z-50" x-cloak>
                        {{-- 遮罩 --}}
                        <div class="fixed inset-0 bg-black/40"
                             x-show="open"
                             x-transition:enter="transition-opacity ease-in-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition-opacity ease-in-out duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="$store.uiSettings.closeOnOutside ? open = false : null"></div>

                        {{-- 抽屉面板 --}}
                        <div class="fixed right-0 inset-y-0 bg-background border-l border-border shadow-xl"
                             style="width: 400px;"
                             x-show="open"
                             x-transition:enter="transition-transform ease-in-out duration-300"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transition-transform ease-in-out duration-200"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full">

                            {{-- 头部 --}}
                            <div class="flex items-center justify-between border-b border-border px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold">通知</h3>
                                    <template x-if="unreadCount > 0">
                                        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1.5 text-[10px] font-medium text-primary-foreground"
                                              x-text="unreadCount + ' 条未读'"></span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="markAllRead()"
                                            class="text-xs text-muted-foreground hover:text-foreground px-2 py-1 rounded hover:bg-accent transition-colors"
                                            x-show="unreadCount > 0">
                                        全部已读
                                    </button>
                                    <button @click="open = false" class="rounded-sm p-1 hover:bg-accent transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- 筛选 Tab --}}
                            <div class="flex border-b border-border px-4">
                                <template x-for="tab in ['全部', '未读']" :key="tab">
                                    <button type="button"
                                            @click="activeTab = tab"
                                            :class="activeTab === tab ? 'border-primary text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'"
                                            class="px-3 py-2 text-sm font-medium border-b-2 transition-colors -mb-px"
                                            x-text="tab"></button>
                                </template>
                            </div>

                            {{-- 通知列表 --}}
                            <div class="overflow-y-auto" style="max-height: calc(100vh - 110px);">
                                <template x-for="n in filteredNotifications" :key="n.id">
                                    <div class="px-4 py-3 hover:bg-accent/50 transition-colors cursor-pointer border-b border-border/50 last:border-0"
                                         :class="!n.is_read ? 'bg-primary/5' : ''"
                                         @click="markRead(n)">
                                        <div class="flex gap-3">
                                            {{-- 类型图标 --}}
                                            <div class="shrink-0 mt-0.5">
                                                <template x-if="n.type === 1">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-blue-100 text-blue-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.443A2.5 2.5 0 0118 14.11V9a6.002 6.002 0 00-4-5.659V3a2 2 0 10-4 0v.341C7.67 4.165 6 6.388 6 9v5.11c0 .822-.334 1.6-.915 2.196L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                                    </div>
                                                </template>
                                                <template x-if="n.type === 2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-green-100 text-green-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    </div>
                                                </template>
                                                <template x-if="n.type === 3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-orange-100 text-orange-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    </div>
                                                </template>
                                                <template x-if="n.type === 4">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-red-100 text-red-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                                    </div>
                                                </template>
                                                <template x-if="n.type === 5">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-purple-100 text-purple-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- 内容 --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="text-sm font-medium truncate" x-text="n.title"></p>
                                                    <template x-if="!n.is_read">
                                                        <span class="shrink-0 mt-1 h-2 w-2 rounded-full bg-primary"></span>
                                                    </template>
                                                </div>
                                                <p class="text-xs text-muted-foreground mt-0.5 line-clamp-2" x-text="n.content" x-show="n.content"></p>
                                                <p class="text-[11px] text-muted-foreground/70 mt-1" x-text="n.time"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- 空状态 --}}
                                <template x-if="filteredNotifications.length === 0">
                                    <div class="flex flex-col items-center justify-center py-12 text-muted-foreground">
                                        <svg class="w-10 h-10 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.443A2.5 2.5 0 0118 14.11V9a6.002 6.002 0 00-4-5.659V3a2 2 0 10-4 0v.341C7.67 4.165 6 6.388 6 9v5.11c0 .822-.334 1.6-.915 2.196L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        <p class="text-sm">暂无通知</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- 界面设置 --}}
            <button type="button" class="p-2 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors" title="界面设置"
                    @click="$store.uiSettings.open = !$store.uiSettings.open">
                <x-ui.icon name="paintbrush" class="w-5 h-5" />
            </button>

            {{-- 分隔线 --}}
            <div class="h-6 w-px bg-border mx-1"></div>

            {{-- 用户下拉菜单 --}}
            <div x-data="{ userMenu: false }" class="relative">
                <button type="button" @click="userMenu = !userMenu"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors">
                    <x-ui.avatar size="sm">{{ auth()->user()->name ?? auth()->user()->username ?? 'U' }}</x-ui.avatar>
                    <span class="hidden sm:inline text-sm font-medium">{{ auth()->user()->username }}</span>
                    <x-ui.icon name="chevron-down" class="w-4 h-4 text-muted-foreground" />
                </button>

                {{-- 下拉内容 --}}
                <div x-show="userMenu"
                     @click.away="userMenu = false"
                     @keydown.escape.window="if(userMenu) userMenu = false"
                     x-transition:enter="ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 top-full mt-2 z-50 min-w-[8rem] overflow-hidden rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md origin-top-right">

                    <a href="{{ route('profile') }}" class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground">
                        <span class="flex items-center gap-2">
                            <x-ui.icon name="user" class="w-4 h-4" />
                            个人中心
                        </span>
                    </a>

                    <div role="separator" class="-mx-1 my-1 h-px bg-border"></div>

                    <button type="button" onclick="document.getElementById('logout-form').submit();"
                            class="relative flex w-full cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors text-red-600 focus:bg-red-50">
                        <span class="flex items-center gap-2">
                            <x-ui.icon name="logout" class="w-4 h-4" />
                            退出登录
                        </span>
                    </button>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </div>
    </div>

    {{-- 界面设置 Livewire 组件（渲染 Drawer + 持久化） --}}
    <livewire:system.ui-settings />
</header>
