<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-foreground">客户订单</h1>
                <p class="text-muted-foreground mt-1">订单全流程：下单→拣货→配送→签收→锁定</p>
            </div>

            {{-- 状态流转 Hover Card --}}
            <div x-data="{ show: false, timer: null, top: 0, left: 0 }"
                 @mouseenter="timer = setTimeout(() => { $refs.card && (function(){ const r = $el.getBoundingClientRect(); top = r.bottom + 6; left = r.left; show = true })() }, 200)"
                 @mouseleave="clearTimeout(timer); show = false"
                 class="relative inline-flex items-center">
                <div class="cursor-help inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                    <x-ui.icon name="arrow-path" class="w-3.5 h-3.5" />
                    状态流转
                </div>
                <template x-teleport="body">
                    <div x-show="show"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         x-cloak
                         x-ref="card"
                         @mouseenter="show = true; clearTimeout(timer)"
                         @mouseleave="show = false"
                         class="fixed z-50 w-96 rounded-lg border border-border bg-popover text-popover-foreground shadow-lg outline-none"
                         :style="'top:' + top + 'px;left:' + left + 'px'">
                        <div class="p-6">
                            <div class="text-sm font-semibold text-foreground mb-3">客户订单状态流转</div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700">1 待拣货</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-blue-100 text-blue-700">2 拣货中</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-orange-100 text-orange-700">3 配送中</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-green-100 text-green-700">4 已签收</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-green-100 text-green-700">5 已锁定</span>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-[11px] text-muted-foreground">任意阶段</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-red-100 text-red-700">9 已作废</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- 流程说明 Hover Card --}}
            <div x-data="{ show: false, timer: null, top: 0, left: 0 }"
                 @mouseenter="timer = setTimeout(() => { $refs.card && (function(){ const r = $el.getBoundingClientRect(); top = r.bottom + 6; left = r.left; show = true })() }, 200)"
                 @mouseleave="clearTimeout(timer); show = false"
                 class="relative inline-flex items-center">
                <div class="cursor-help inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                    <x-ui.icon name="information-circle" class="w-3.5 h-3.5" />
                    流程说明
                </div>
                <template x-teleport="body">
                    <div x-show="show"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         x-cloak
                         x-ref="card"
                         @mouseenter="show = true; clearTimeout(timer)"
                         @mouseleave="show = false"
                         class="fixed z-50 w-96 rounded-lg border border-border bg-popover text-popover-foreground shadow-lg outline-none"
                         :style="'top:' + top + 'px;left:' + left + 'px'">
                        <div class="p-6">
                            <div class="text-sm font-semibold text-foreground mb-4">客户订单流程说明</div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700 shrink-0">待拣货</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">客户下单后等待系统分配拣货任务</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-blue-100 text-blue-700 shrink-0">拣货中</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">拣货员正在按单拣选商品</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-orange-100 text-orange-700 shrink-0">配送中</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">商品已交接给配送员，正在配送途中</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-green-100 text-green-700 shrink-0">已签收</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">客户确认收货，订单完成签收</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-green-100 text-green-700 shrink-0">已锁定</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">订单已结算锁定，不可再修改</span>
                                </div>
                                <div class="border-t border-border pt-3 mt-1">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-red-100 text-red-700 shrink-0">已作废</span>
                                        <span class="text-[11px] leading-snug text-muted-foreground">订单中途作废失效</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @can('order.order.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增订单
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索订单号/商家..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待拣货</option>
            <option value="2">拣货中</option>
            <option value="3">配送中</option>
            <option value="4">已签收</option>
            <option value="5">已锁定</option>
            <option value="9">已作废</option>
        </select>
        <select wire:model.live="filterPaymentStatus" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部支付</option>
            <option value="1">未支付</option>
            <option value="2">已支付</option>
            <option value="3">账期</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('order.order.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'order_no')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 160px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>订单号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($orders as $order)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="order-{{ $order->id }}">
                <div><input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-mono font-medium truncate">
                    <a href="{{ route('orders.detail', $order->id) }}" class="text-blue-600 hover:underline">{{ $order->order_no }}</a>
                </div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $order->id }}</div>
                            @break
                        @case('merchant_id')
                            <div class="text-sm text-foreground truncate">{{ $order->merchant?->name ?? '-' }}</div>
                            @break
                        @case('total_amount')
                            <div class="text-sm text-foreground">{{ money_format($order->total_amount) }}</div>
                            @break
                        @case('status')
                            <div>{!! status_badge($order->status, 'order') !!}</div>
                            @break
                        @case('payment_status')
                            <div>{!! status_badge($order->payment_status, 'order_payment') !!}</div>
                            @break
                        @case('order_date')
                            <div class="text-sm text-foreground">{{ $order->order_date?->format('Y-m-d') ?? '-' }}</div>
                            @break
                        @case('delivery_date')
                            <div class="text-sm text-foreground">{{ $order->delivery_date?->format('Y-m-d') ?? '-' }}</div>
                            @break
                        @case('contact_name')
                            <div class="text-sm text-foreground truncate">{{ $order->contact_name ?? '-' }}</div>
                            @break
                        @case('settlement_type')
                            <div class="text-sm text-foreground">{{ \App\Livewire\Order\OrderList::$settlementTypeMap[$order->settlement_type] ?? '-' }}</div>
                            @break
                        @case('remark')
                            <div class="text-sm text-foreground truncate max-w-[200px]">{{ $order->remark ?: '-' }}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $order->created_at?->format('Y-m-d H:i:s') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $order->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('order.order.edit')
                    @if(!in_array($order->status, [4, 5, 9]))
                    <button type="button" wire:click="openEditModal({{ $order->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('order.order.create')
                    @if($order->status === 1)
                    <button type="button" wire:click="confirmOrder({{ $order->id }})" class="text-green-600 hover:text-green-700 text-sm">确认</button>
                    @endif
                    @endcan
                    @can('order.order.create')
                    @if(!in_array($order->status, [4, 5, 9]))
                    <button type="button" wire:click="cancelOrder({{ $order->id }})" class="text-orange-600 hover:text-orange-700 text-sm">作废</button>
                    @endif
                    @endcan
                    @can('order.order.create')
                    @if($order->status === 3)
                    <button type="button" wire:click="completeOrder({{ $order->id }})" class="text-green-600 hover:text-green-700 text-sm">完成</button>
                    @endif
                    @endcan
                    @can('order.order.delete')
                    <button type="button" wire:click="confirmDelete({{ $order->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无订单数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑订单' : '新增订单' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                @if(!$editingId)
                {{-- 创建模式：完整表单 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商家 <span class="text-red-500">*</span></label>
                    <select wire:model="formMerchantId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择商家</option>
                        @foreach($merchants as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                    @error('formMerchantId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">结算方式 <span class="text-red-500">*</span></label>
                        <select wire:model="formSettlementType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">现结</option>
                            <option value="2">账期</option>
                            <option value="3">预付款</option>
                        </select>
                        @error('formSettlementType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">配送批次 <span class="text-red-500">*</span></label>
                        <select wire:model="formBatch" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">上午</option>
                            <option value="2">下午</option>
                        </select>
                        @error('formBatch') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">单据日期</label>
                        <input type="date" wire:model="formOrderDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('formOrderDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">收货日期</label>
                        <input type="date" wire:model="formDeliveryDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('formDeliveryDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">联系人</label>
                        <input type="text" wire:model="formContactName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="收货联系人" />
                        @error('formContactName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">联系电话</label>
                        <input type="text" wire:model="formContactPhone" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="联系电话" />
                        @error('formContactPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">配送地址</label>
                    <input type="text" wire:model="formDeliveryAddress" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="配送地址" />
                    @error('formDeliveryAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
