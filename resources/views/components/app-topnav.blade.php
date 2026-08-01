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
            {{-- 主导航（从 config/menu.php 读取，@can 过滤不可见菜单） --}}
            <x-nav-menu class="hidden md:flex">

                @php
                    $menuConfig = config('menu');
                    $iconMap = [
                        'product' => 'cube',
                        'purchase' => 'cart',
                        'order' => 'clipboard',
                        'inventory' => 'chart-bar',
                        'finance' => 'banknotes',
                        'user' => 'users',
                        'org' => 'building',
                        'system' => 'adjustments-horizontal',
                    ];
                    $linkIconMap = [
                        'product.category' => 'swatch', 'product.product' => 'cube', 'product.sku' => 'tag',
                        'product.visibility' => 'eye', 'product.keyword' => 'hashtag', 'product.barcode' => 'qr-code',
                        'product.supplier' => 'squares',
                        'purchase.item' => 'clipboard-document-list', 'purchase.order' => 'document-text',
                        'purchase.return' => 'arrow-uturn-left',
                        'order.order' => 'clipboard-document-list', 'order.cart' => 'shopping-bag',
                        'order.frequent' => 'star', 'order.repurchase' => 'arrow-path',
                        'order.delivery' => 'truck', 'order.signature' => 'check-badge',
                        'order.discrepancy' => 'exclamation-triangle', 'order.return' => 'arrow-uturn-left',
                        'inventory.warehouse' => 'building-office', 'inventory.stock' => 'chart-bar',
                        'inventory.log' => 'document-text', 'inventory.picking' => 'clipboard-document-list',
                        'finance.account' => 'wallet', 'finance.recharge' => 'bank',
                        'finance.settlement' => 'briefcase', 'finance.receivable' => 'banknotes',
                        'finance.invoice' => 'document', 'finance.correction' => 'pencil',
                        'finance.price-strategy' => 'shield', 'finance.apportionment' => 'pie-chart',
                        'finance.loss' => 'trash',
                        'user.manage' => 'users', 'user.role' => 'shield-check', 'user.permission' => 'key',
                        'org.supplier' => 'building-office', 'org.merchant' => 'store',
                        'org.route' => 'signal', 'org.driver' => 'user-circle', 'org.vehicle' => 'truck',
                        'system.config' => 'adjustments-horizontal', 'system.banner' => 'photo',
                        'system.promotion' => 'sparkles', 'system.operation' => 'document-text',
                        'system.audit' => 'shield-exclamation', 'system.approval' => 'check-circle',
                        'system.price-log' => 'document-text', 'system.login-log' => 'key',
                        'system.wechat' => 'chat-bubble-left-right',
                    ];
                @endphp

                @foreach($menuConfig as $group)
                    @php
                        $visibleChildren = collect($group['children'])
                            ->filter(fn($item) => auth()->user()->can($item['permission']));
                    @endphp

                    @if($visibleChildren->isNotEmpty())
                        <x-nav-menu-item value="{{ $group['key'] }}">
                            <x-nav-menu-trigger icon="{{ $iconMap[$group['key']] ?? 'folder' }}">{{ $group['label'] }}</x-nav-menu-trigger>
                            <x-nav-menu-content value="{{ $group['key'] }}">
                                @foreach($visibleChildren as $item)
                                    <x-nav-menu-icon-link
                                        href="{{ route($item['route']) }}"
                                        icon="{{ $linkIconMap[$item['key']] ?? 'document' }}"
                                        description="{{ $item['description'] ?? '' }}"
                                    >{{ $item['label'] }}</x-nav-menu-icon-link>
                                @endforeach
                            </x-nav-menu-content>
                        </x-nav-menu-item>
                    @endif
                @endforeach

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
