<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-foreground">采购单管理</h1>
                <p class="text-muted-foreground mt-1">采购单全流程：创建→接单→发货→入库→完成</p>
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
                            <div class="text-sm font-semibold text-foreground mb-3">采购单状态流转</div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700">1 待接单</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-blue-100 text-blue-700">2 备货中</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-orange-100 text-orange-700">3 已发货</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-green-100 text-green-700">4 已入库</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-green-100 text-green-700">5 完成</span>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-[11px] text-muted-foreground">任意阶段</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-red-100 text-red-700">9 已作废</span>
                            </div>
                            <div class="text-[11px] text-muted-foreground mt-3 leading-relaxed border-t border-border pt-3">
                                已入库(4)只能→完成(5)，已入库如需作废须走退货流程
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
                            <div class="text-sm font-semibold text-foreground mb-4">采购单流程说明</div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700 shrink-0">待接单</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">创建采购单后进入待接单状态，等待供应商确认接单</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-blue-100 text-blue-700 shrink-0">备货中</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">供应商接单后开始备货，准备采购商品</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-orange-100 text-orange-700 shrink-0">已发货</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">供应商备货完成，将商品发出配送</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-green-100 text-green-700 shrink-0">已入库</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">商品送达仓库并完成入库登记</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-green-100 text-green-700 shrink-0">完成</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">采购单全流程结束，单据完结</span>
                                </div>
                                <div class="border-t border-border pt-3 mt-1">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-red-100 text-red-700 shrink-0">已作废</span>
                                        <span class="text-[11px] leading-snug text-muted-foreground">采购单中途作废失效；已入库状态须走退货流程</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @can('purchase.purchase-order.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增采购单
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索单号/供应商..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待接单</option>
            <option value="2">备货中</option>
            <option value="3">已发货</option>
            <option value="4">已入库</option>
            <option value="5">完成</option>
            <option value="9">已作废</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
            @if($selectedCount > 0)
                <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
                <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
                <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
            @endif
    </div>

    {{-- 采购单列表 --}}
    @php
        $visibleCols = collect($this->getAllColumns())->filter(fn($col) => $this->isColumnVisible($col['key']));
    @endphp
    <div class="rounded-lg border bg-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2.5 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    @foreach($visibleCols as $col)
                        <th class="px-4 py-2.5 text-left">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-2.5 text-right w-24">操作</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="po-{{ $order->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    @foreach($visibleCols as $col)
                        @switch($col['key'])
                            @case('order_no')
                                <td class="px-4 py-2">
                                    <a href="{{ route('purchase-orders.detail', $order->id) }}" class="font-mono text-blue-600 hover:underline">{{ $order->order_no }}</a>
                                </td>
                                @break
                            @case('supplier_id')
                                <td class="px-4 py-2">{{ $order->supplier?->name ?? '-' }}</td>
                                @break
                            @case('purchase_date')
                                <td class="px-4 py-2 text-foreground">{{ $order->purchase_date?->format('Y-m-d') ?? '-' }}</td>
                                @break
                            @case('status')
                                <td class="px-4 py-2">{!! status_badge($order->status, 'purchase_order') !!}</td>
                                @break
                            @case('total_amount')
                                <td class="px-4 py-2 text-right">{{ money_format($order->total_amount) }}</td>
                                @break
                            @case('actual_amount')
                                <td class="px-4 py-2 text-right">{{ money_format($order->actual_amount) }}</td>
                                @break
                            @case('created_at')
                                <td class="px-4 py-2 text-muted-foreground">{{ $order->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                @break
                            @default
                                <td class="px-4 py-2 text-foreground">{{ $order->{$col['key']} ?? '-' }}</td>
                        @endswitch
                    @endforeach
                    <td class="px-4 py-2 text-right">
                        <div class="inline-flex items-center gap-0.5">
                            {{-- 编辑 --}}
                            @can('purchase.purchase-order.edit')
                            @if(!in_array($order->status, [5, 9]))
                            <button type="button" wire:click="openEditModal({{ $order->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endif
                            @endcan
                            {{-- 删除（未入库状态可删除） --}}
                            @can('purchase.purchase-order.delete')
                            @if(in_array($order->status, [1, 2, 3]))
                                <button type="button" wire:click="confirmDelete({{ $order->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ $visibleCols->count() + 2 }}" class="px-4 py-10 text-center text-muted-foreground">暂无采购单数据</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑采购单' : '新增采购单' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">供应商 <span class="text-red-500">*</span></label>
                    <select wire:model="formSupplierId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择供应商</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                    @error('formSupplierId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">采购日期</label>
                    <input type="date" wire:model="formPurchaseDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
