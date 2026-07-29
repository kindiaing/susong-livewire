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
                        <x-nav-menu-icon-link href="{{ route('categories') }}" icon="swatch" description="商品分类层级树">分类管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('products') }}" icon="cube" description="商品信息与图片维护">商品管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('skus') }}" icon="tag" description="规格、价格、库存单位">SKU 管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('merchant-addresses') }}" icon="eye" description="商家可见SKU配置">可见性配置</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('tags') }}" icon="hashtag" description="商品搜索与标签">关键词标签</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('sku-barcodes') }}" icon="qr-code" description="SKU条码绑定">条码管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('sku-suppliers') }}" icon="squares" description="多供应商供应关系">一品多供</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 采购管理 --}}
                <x-nav-menu-item value="purchase">
                    <x-nav-menu-trigger icon="cart">采购管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="purchase">
                        <x-nav-menu-icon-link href="{{ route('purchase-items') }}" icon="clipboard-document-list" description="采购需求汇总">待采清单</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('purchase-orders') }}" icon="document-text" description="采购订单创建与跟踪">采购单管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('purchase-returns') }}" icon="arrow-uturn-left" description="退货给供应商">采购退货</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 订单配送 --}}
                <x-nav-menu-item value="order">
                    <x-nav-menu-trigger icon="clipboard">订单配送</x-nav-menu-trigger>
                    <x-nav-menu-content value="order">
                        <x-nav-menu-icon-link href="{{ route('orders') }}" icon="clipboard-document-list" description="商家下单记录">客户订单</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('carts') }}" icon="shopping-bag" description="商家选购暂存">购物车</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('frequently-bought') }}" icon="star" description="商家频繁购买记录">常购清单</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('repurchase-templates') }}" icon="arrow-path" description="一键快速复购">复购模板</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('delivery-tasks') }}" icon="truck" description="司机配送调度">配送任务</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('signatures') }}" icon="check-badge" description="签收照片与温度记录">签收存证</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('discrepancies') }}" icon="exclamation-triangle" description="配送差异与短少处理">差异处理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('order-returns') }}" icon="arrow-uturn-left" description="客户退货处理">售后退货</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 库存拣货 --}}
                <x-nav-menu-item value="inventory">
                    <x-nav-menu-trigger icon="chart-bar">库存拣货</x-nav-menu-trigger>
                    <x-nav-menu-content value="inventory">
                        <x-nav-menu-icon-link href="{{ route('warehouses') }}" icon="building-office" description="仓库信息与分区">仓库管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('inventories') }}" icon="chart-bar" description="各仓库SKU存量">实时库存</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('inventory-logs') }}" icon="document-text" description="出入库变动记录">库存日志</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('picking-tasks') }}" icon="clipboard-document-list" description="拣货分配与执行">拣货任务</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 财务管理 --}}
                <x-nav-menu-item value="finance">
                    <x-nav-menu-trigger icon="banknotes">财务管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="finance">
                        <x-nav-menu-icon-link href="{{ route('merchant-accounts') }}" icon="wallet" description="商家账户余额与额度">客户账户</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('recharges') }}" icon="bank" description="充值记录与审核">客户充值</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('supplier-settlements') }}" icon="briefcase" description="采购结算与付款">供应商结算</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('receivables') }}" icon="banknotes" description="客户欠款与收款">应收账款</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('invoices') }}" icon="document" description="开票记录与审核">发票管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('correction-authorizations') }}" icon="pencil" description="账目更正审批">授权更正</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('price-strategies') }}" icon="shield" description="客户定价规则">价格策略</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('price-apportionments') }}" icon="pie-chart" description="费用分摊配置">费用均摊</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('loss-orders') }}" icon="trash" description="损耗记录与审核">损耗管理</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 用户权限 --}}
                <x-nav-menu-item value="user">
                    <x-nav-menu-trigger icon="users">用户权限</x-nav-menu-trigger>
                    <x-nav-menu-content value="user">
                        <x-nav-menu-icon-link href="#" icon="users" description="用户CRUD、重置密码、禁用启用">用户管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('roles') }}" icon="shield-check" description="9个系统角色、权限配置">角色管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('permissions') }}" icon="key" description="模块/页面/按钮级权限控制">权限管理</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 组织主体 --}}
                <x-nav-menu-item value="org">
                    <x-nav-menu-trigger icon="building">组织主体</x-nav-menu-trigger>
                    <x-nav-menu-content value="org">
                        <x-nav-menu-icon-link href="{{ route('suppliers') }}" icon="building-office" description="供应商信息与资质">供应商管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('merchants') }}" icon="store" description="商家信息与账户">商家管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('delivery-routes') }}" icon="signal" description="配送区域与线路">配送线路</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('drivers') }}" icon="user-circle" description="司机信息与排班">司机管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('vehicles') }}" icon="truck" description="车辆信息与司机绑定">车辆管理</x-nav-menu-icon-link>
                    </x-nav-menu-content>
                </x-nav-menu-item>

                {{-- 系统管理 --}}
                <x-nav-menu-item value="system">
                    <x-nav-menu-trigger icon="adjustments-horizontal">系统管理</x-nav-menu-trigger>
                    <x-nav-menu-content value="system">
                        <x-nav-menu-icon-link href="{{ route('settings') }}" icon="adjustments-horizontal" description="6组17项系统配置">系统配置</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('banners') }}" icon="photo" description="首页轮播图管理">轮播广告</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('promotions') }}" icon="sparkles" description="推荐商品配置">运营主推</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('operation-logs') }}" icon="document-text" description="按操作人/时间/模块筛选">操作日志</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('audit-logs') }}" icon="shield-exclamation" description="敏感操作审计记录">审计日志</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('approval-config') }}" icon="check-circle" description="19个审核节点开关与列表">审核管理</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('price-change-logs') }}" icon="document-text" description="价格变更历史">改价记录</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('login-logs') }}" icon="key" description="用户登录记录">登录日志</x-nav-menu-icon-link>
                        <x-nav-menu-icon-link href="{{ route('wechat-users') }}" icon="chat-bubble-left-right" description="小程序用户绑定">微信用户</x-nav-menu-icon-link>
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
