@props([])
{{--
  Command 搜索组件（Alpine.js）
  参考 shadcn/ui Command 组件风格
  支持 Ctrl+K / Cmd+K 快捷键唤起
  ESC 关闭
--}}
<div x-data="commandPalette()" x-init="init()">

    {{-- 触发按钮 --}}
    <button type="button" @click="open = true"
            class="flex items-center gap-2 h-8 px-3 rounded-md border border-input bg-background text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors cursor-pointer"
            title="搜索 (Ctrl+K)">
        <x-ui.icon name="magnifying-glass" class="w-4 h-4" />
        <span class="hidden sm:inline">搜索...</span>
        <kbd class="hidden sm:inline-flex items-center gap-0.5 h-5 px-1.5 ml-2 rounded border border-border bg-muted text-[10px] font-medium text-muted-foreground">
            <span>⌘</span>K
        </kbd>
    </button>

    {{-- 弹窗 --}}
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-50" x-cloak>
            {{-- 遮罩 --}}
            <div class="fixed inset-0 bg-black/40"
                 x-show="open"
                 x-transition:enter="transition-opacity ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"></div>

            {{-- 弹窗主体 --}}
            <div class="fixed inset-x-0 top-[15%] mx-auto max-w-xl px-4"
                 x-show="open"
                 x-transition:enter="ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <div class="overflow-hidden rounded-lg border bg-popover text-popover-foreground shadow-xl">
                    {{-- 搜索输入框 --}}
                    <div class="flex items-center border-b px-3">
                        <x-ui.icon name="magnifying-glass" class="w-4 h-4 shrink-0 text-muted-foreground" />
                        <input type="text"
                               x-ref="searchInput"
                               x-model="query"
                               @keydown.down.prevent="navigateDown()"
                               @keydown.up.prevent="navigateUp()"
                               @keydown.enter.prevent="selectItem()"
                               placeholder="搜索菜单、功能、页面..."
                               class="flex-1 h-12 bg-transparent px-3 text-sm outline-none placeholder:text-muted-foreground" />
                        <kbd class="flex items-center h-5 px-1.5 rounded border border-border bg-muted text-[10px] font-medium text-muted-foreground cursor-pointer"
                             @click="open = false">ESC</kbd>
                    </div>

                    {{-- 搜索结果 --}}
                    <div class="max-h-80 overflow-y-auto py-2">
                        {{-- 无结果 --}}
                        <template x-if="filteredGroups.length === 0">
                            <div class="py-6 text-center text-sm text-muted-foreground">
                                没有找到匹配的结果
                            </div>
                        </template>

                        <template x-for="group in filteredGroups" :key="group.label">
                            <div class="px-2 pt-2 first:pt-0">
                                <p class="px-2 mb-1 text-xs font-medium text-muted-foreground" x-text="group.label"></p>
                                <template x-for="(item, idx) in group.items" :key="item.label">
                                    <button type="button"
                                            @click="goTo(item)"
                                            @mouseenter="activeIndex = group.label + '-' + idx"
                                            :class="activeIndex === group.label + '-' + idx ? 'bg-accent text-accent-foreground' : 'text-popover-foreground'"
                                            class="flex w-full items-center gap-3 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors cursor-pointer hover:bg-accent hover:text-accent-foreground">
                                        <span class="shrink-0">
                                            <x-ui.icon :name="item.icon || 'document'" class="w-4 h-4 text-muted-foreground" />
                                        </span>
                                        <span class="flex-1 truncate" x-text="item.label"></span>
                                        <span x-show="item.shortcut" class="text-xs text-muted-foreground" x-text="item.shortcut"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- 底部提示 --}}
                    <div class="flex items-center justify-between border-t px-3 py-2 text-xs text-muted-foreground">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1"><kbd class="rounded border border-border bg-muted px-1 py-0.5 text-[10px]">↑↓</kbd> 导航</span>
                            <span class="flex items-center gap-1"><kbd class="rounded border border-border bg-muted px-1 py-0.5 text-[10px]">↵</kbd> 选择</span>
                        </div>
                        <span>Command Palette</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        function commandPalette() {
            return {
                open: false,
                query: '',
                activeIndex: null,

                // 搜索数据源（菜单+功能+页面）
                groups: [
                    {
                        label: '商品管理',
                        items: [
                            { label: '分类管理', icon: 'swatch', url: '#' },
                            { label: '商品管理', icon: 'cube', url: '#' },
                            { label: 'SKU 管理', icon: 'tag', url: '#' },
                            { label: '条码管理', icon: 'qr-code', url: '#' },
                        ]
                    },
                    {
                        label: '采购管理',
                        items: [
                            { label: '待采清单', icon: 'clipboard', url: '#' },
                            { label: '采购单管理', icon: 'document', url: '#' },
                            { label: '采购退货', icon: 'arrow-uturn-left', url: '#' },
                        ]
                    },
                    {
                        label: '订单配送',
                        items: [
                            { label: '客户订单', icon: 'lock-closed', url: '#' },
                            { label: '配送任务', icon: 'truck', url: '#' },
                            { label: '签收存证', icon: 'check-badge', url: '#' },
                            { label: '差异处理', icon: 'exclamation-triangle', url: '#' },
                            { label: '售后退货', icon: 'arrow-uturn-left', url: '#' },
                        ]
                    },
                    {
                        label: '库存拣货',
                        items: [
                            { label: '仓库管理', icon: 'building', url: '#' },
                            { label: '实时库存', icon: 'chart-bar', url: '#' },
                            { label: '库存日志', icon: 'document', url: '#' },
                            { label: '库存预警', icon: 'bell', url: '#' },
                            { label: '拣货任务', icon: 'cube', url: '#' },
                        ]
                    },
                    {
                        label: '财务管理',
                        items: [
                            { label: '客户账户', icon: 'wallet', url: '#' },
                            { label: '客户充值', icon: 'bank', url: '#' },
                            { label: '供应商结算', icon: 'briefcase', url: '#' },
                            { label: '应收账款', icon: 'banknotes', url: '#' },
                            { label: '损耗管理', icon: 'trash', url: '#' },
                        ]
                    },
                    {
                        label: '系统管理',
                        items: [
                            { label: '系统配置', icon: 'cog', url: '{{ route("settings") }}' },
                            { label: '个人中心', icon: 'user', url: '{{ route("profile") }}' },
                            { label: '操作日志', icon: 'document', url: '#' },
                            { label: '审计日志', icon: 'shield', url: '#' },
                        ]
                    },
                ],

                get filteredGroups() {
                    if (!this.query.trim()) return this.groups;
                    const q = this.query.toLowerCase();
                    return this.groups
                        .map(g => ({
                            ...g,
                            items: g.items.filter(item =>
                                item.label.toLowerCase().includes(q)
                            )
                        }))
                        .filter(g => g.items.length > 0);
                },

                init() {
                    // Ctrl+K / Cmd+K 快捷键
                    window.addEventListener('keydown', (e) => {
                        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                            e.preventDefault();
                            this.open = !this.open;
                        }
                        if (e.key === 'Escape' && this.open) {
                            this.open = false;
                        }
                    });

                    // 打开时聚焦输入框
                    this.$watch('open', (val) => {
                        if (val) {
                            this.query = '';
                            this.activeIndex = null;
                            this.$nextTick(() => {
                                this.$refs.searchInput?.focus();
                            });
                        }
                    });
                },

                navigateDown() {
                    const all = this.allItems;
                    if (all.length === 0) return;
                    const idx = all.findIndex(k => k === this.activeIndex);
                    this.activeIndex = all[(idx + 1) % all.length];
                },

                navigateUp() {
                    const all = this.allItems;
                    if (all.length === 0) return;
                    const idx = all.findIndex(k => k === this.activeIndex);
                    this.activeIndex = all[(idx - 1 + all.length) % all.length];
                },

                get allItems() {
                    const keys = [];
                    this.filteredGroups.forEach(g => {
                        g.items.forEach((_, idx) => {
                            keys.push(g.label + '-' + idx);
                        });
                    });
                    return keys;
                },

                selectItem() {
                    if (!this.activeIndex) return;
                    const [groupLabel, idxStr] = this.activeIndex.split('-');
                    const group = this.filteredGroups.find(g => g.label === groupLabel);
                    if (!group) return;
                    const item = group.items[parseInt(idxStr)];
                    if (item) this.goTo(item);
                },

                goTo(item) {
                    this.open = false;
                    if (item.url && item.url !== '#') {
                        window.location.href = item.url;
                    }
                }
            };
        }
    </script>
</div>
