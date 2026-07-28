@props([])
{{--
  顶部导航栏组件
  结构：左侧Logo | 中间一级导航（hover展开子菜单）| 右侧功能区
--}}
<header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
    <div class="flex h-14 items-center px-4 gap-4">

        {{-- 左侧：Logo + 系统名称 --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 mr-4 shrink-0">
            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
                <x-ui.icon name="truck-delivery" class="w-5 h-5" />
            </div>
            <span class="font-semibold text-lg hidden sm:inline">本地速送</span>
        </a>

        {{-- 中间：主导航（使用 nav-menu 组件体系） --}}
        <x-nav-menu class="flex-1 hidden md:flex">

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

        {{-- 移动端汉堡菜单按钮 --}}
        <button class="md:hidden p-2 rounded-md hover:bg-accent hover:text-accent-foreground"
                x-data="{ mobileOpen: false }"
                @click="mobileOpen = !mobileOpen">
            <x-ui.icon name="bars" class="w-5 h-5" />
        </button>

        {{-- 右侧功能区 --}}
        <div class="flex items-center gap-1 ml-auto shrink-0">

            {{-- 通知 --}}
            <button type="button" class="relative p-2 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors" title="通知">
                <x-ui.icon name="bell" class="w-5 h-5" />
                {{-- 角标 --}}
                <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-destructive text-[10px] font-medium text-destructive-foreground">3</span>
            </button>

            {{-- 界面设置 --}}
            <button type="button" class="p-2 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors" title="界面设置">
                <x-ui.icon name="paintbrush" class="w-5 h-5" />
            </button>

            {{-- 分隔线 --}}
            <div class="h-6 w-px bg-border mx-1"></div>

            {{-- 用户下拉菜单 --}}
            <x-ui.dropdown-menu align="end">
                <x-slot:trigger>
                    <button type="button" class="flex items-center gap-2 px-2 py-1.5 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors">
                        <x-ui.avatar size="sm">{{ auth()->user()->name ?? auth()->user()->username ?? 'U' }}</x-ui.avatar>
                        <span class="hidden sm:inline text-sm font-medium">{{ auth()->user()->username }}</span>
                    </button>
                </x-slot:trigger>

                <x-ui.dropdown-item href="{{ route('profile') }}">
                    <span class="flex items-center gap-2">
                        <x-ui.icon name="user" class="w-4 h-4" />
                        个人中心
                    </span>
                </x-ui.dropdown-item>
                <x-ui.dropdown-separator />
                <x-ui.dropdown-item href="#" destructive onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="flex items-center gap-2">
                        <x-ui.icon name="logout" class="w-4 h-4" />
                        退出登录
                    </span>
                </x-ui.dropdown-item>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </x-ui.dropdown-menu>
        </div>
    </div>
</header>
