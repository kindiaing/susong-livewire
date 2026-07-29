@props([])
{{--
  顶部导航栏组件
  布局：导航栏撑满全宽，Logo靠最左，个人中心靠最右，导航+搜索在中间整体居中
--}}
<header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
    <div class="flex h-14 items-center px-4 w-full">

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
                        <x-nav-menu-icon-link href="{{ route('categories') }}" icon="swatch">分类管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('products') }}" icon="cube">商品管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('skus') }}" icon="tag">SKU 管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="#" icon="eye">可见性配置</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('tags') }}" icon="hashtag">关键词标签</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('sku-barcodes') }}" icon="qr-code">条码管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('sku-suppliers') }}" icon="squares">一品多供</x-nav-menu-icon-link>
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
                        <x-nav-menu-link href="{{ route('roles') }}" description="9个系统角色、权限配置（树形结构）">角色管理</x-nav-menu-link>
                        <x-nav-menu-link href="{{ route('permissions') }}" description="权限树形列表、模块/页面/按钮级控制">权限管理</x-nav-menu-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 组织主体 --}}
                <x-nav-menu-item value="org">
                    <x-nav-menu-trigger icon="building">组织主体</x-nav-menu-trigger>
                    <x-nav-menu-content value="org">
                        <x-nav-menu-icon-link href="{{ route('suppliers') }}" icon="building-office">供应商管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('merchants') }}" icon="store">商家管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('delivery-routes') }}" icon="signal">配送线路</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('drivers') }}" icon="user-circle">司机管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('vehicles') }}" icon="truck">车辆管理</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 系统管理 --}}
                <x-nav-menu-item value="system">
                    <x-nav-menu-trigger icon="adjustments-horizontal">系统管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="system">
                        <x-nav-menu-link href="{{ route('settings') }}" description="6组17项系统配置，分组导航+列表编辑">系统配置</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="首页轮播广告管理">轮播广告</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="主推商品/品类配置">运营主推</x-nav-menu-link>
                        <x-nav-menu-link href="{{ route('operation-logs') }}" description="按操作人/时间/模块筛选">操作日志</x-nav-menu-link>
                        <x-nav-menu-link href="{{ route('audit-logs') }}" description="敏感操作审计，保留策略0-180天">审计日志</x-nav-menu-link>
                        <x-nav-menu-link href="{{ route('approval-config') }}" description="19个审核节点开关、审核列表">审核管理</x-nav-menu-link>
                        <x-nav-menu-link href="#" description="改价记录列表/详情、数据快照回溯">改价记录</x-nav-menu-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

            </x-nav-menu>

            {{-- Command 搜索 --}}
            <div class="hidden md:block">
                <x-ui.command />
            </div>
        </div>

        {{-- 右侧功能区（靠近右端） --}}
        <div class="flex items-center gap-1 shrink-0 ml-4" x-data>

            {{-- 通知 Drawer（Livewire 组件） --}}
            <livewire:system.notification-drawer />

            {{-- 界面设置 Drawer --}}
            <button type="button" class="p-2 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors" title="界面设置"
                    @click="$store.uiSettings.open = !$store.uiSettings.open">
                <x-ui.icon name="cog-6-tooth" class="w-5 h-5" />
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
