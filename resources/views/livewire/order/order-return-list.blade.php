<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-foreground">售后退货</h1>
                <p class="text-muted-foreground mt-1">退货全流程：申请→审核→退货→退款</p>
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
                         x-cloak x-ref="card"
                         @mouseenter="show = true; clearTimeout(timer)"
                         @mouseleave="show = false"
                         class="fixed z-50 w-96 rounded-lg border border-border bg-popover text-popover-foreground shadow-lg outline-none"
                         :style="'top:' + top + 'px;left:' + left + 'px'">
                        <div class="p-6">
                            <div class="text-sm font-semibold text-foreground mb-3">售后退货状态流转</div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700">1 待审核</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-blue-100 text-blue-700">2 已审核</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-orange-100 text-orange-700">3 已退货</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-green-100 text-green-700">4 退款完成</span>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-[11px] text-muted-foreground">待审核</span>
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
                         x-cloak x-ref="card"
                         @mouseenter="show = true; clearTimeout(timer)"
                         @mouseleave="show = false"
                         class="fixed z-50 w-96 rounded-lg border border-border bg-popover text-popover-foreground shadow-lg outline-none"
                         :style="'top:' + top + 'px;left:' + left + 'px'">
                        <div class="p-6">
                            <div class="text-sm font-semibold text-foreground mb-4">售后退货流程说明</div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700 shrink-0">待审核</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">客户提交退货申请，等待审核</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-blue-100 text-blue-700 shrink-0">已审核</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">退货审核通过，等待退货取件</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-orange-100 text-orange-700 shrink-0">已退货</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">商品已退回，等待退款处理</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-green-100 text-green-700 shrink-0">退款完成</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">退款已完成，退货流程结束</span>
                                </div>
                                <div class="border-t border-border pt-3 mt-1">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-red-100 text-red-700 shrink-0">已作废</span>
                                        <span class="text-[11px] leading-snug text-muted-foreground">退货单中途作废失效；已审核后不可作废</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @can('order.order-return.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增退货
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索退货单号/商家..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待审核</option>
            <option value="2">已审核</option>
            <option value="3">已退货</option>
            <option value="4">退款完成</option>
            <option value="9">已作废</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('order.order-return.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'order_id')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 180px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>退货单号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="order-return-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-mono font-medium text-foreground truncate">{{ $item->return_no }}</div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('merchant_id')
                            <div class="text-sm text-foreground truncate">{{ $item->merchant?->name ?? '-' }}</div>
                            @break
                        @case('reason')
                            <div class="text-sm text-foreground truncate max-w-[200px]">{{ $item->reason ?? '-' }}</div>
                            @break
                        @case('status')
                            <div>
                                @php $sc = \App\Livewire\Order\OrderReturnList::$statusColorMap; $c = $sc[$item->status] ?? 'gray'; @endphp
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ \App\Livewire\Order\OrderReturnList::$statusMap[$item->status] ?? '-' }}</span>
                            </div>
                            @break
                        @case('refund_amount')
                            <div class="text-sm text-foreground">{{ money_format($item->refund_amount) }}</div>
                            @break
                        @case('note')
                            <div class="text-sm text-foreground truncate max-w-[200px]">{{ $item->note ?: '-' }}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('order.order-return.edit')
                    @if(!in_array($item->status, [3, 4, 9]))
                    <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('order.order-return.create')
                    @if($item->status === 1)
                    <button type="button" wire:click="approveReturn({{ $item->id }})" class="text-green-600 hover:text-green-700 text-sm">通过</button>
                    <button type="button" wire:click="rejectReturn({{ $item->id }})" class="text-orange-600 hover:text-orange-700 text-sm">拒绝</button>
                    @endif
                    @endcan
                    @can('order.order-return.create')
                    @if($item->status === 2)
                    <button type="button" wire:click="startReturn({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">开始退货</button>
                    @endif
                    @endcan
                    @can('order.order-return.create')
                    @if($item->status === 3)
                    <button type="button" wire:click="completeReturn({{ $item->id }})" class="text-green-600 hover:text-green-700 text-sm">完成</button>
                    @endif
                    @endcan
                    @can('order.order-return.delete')
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无退货数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑退货单' : '新增退货' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                @if(!$editingId)
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">订单 <span class="text-red-500">*</span></label>
                    <select wire:model="formOrderId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择订单</option>
                        @foreach(\App\Models\Order::orderBy('id', 'desc')->limit(100)->get() as $o)
                        <option value="{{ $o->id }}">{{ $o->order_no }}</option>
                        @endforeach
                    </select>
                    @error('formOrderId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
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
                @endif
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">退货原因 {{ !$editingId ? '<span class="text-red-500">*</span>' : '' }}</label>
                    <input type="text" wire:model="formReason" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入退货原因" />
                    @error('formReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formNote" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formNote') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
