<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-foreground">采购退货</h1>
                <p class="text-muted-foreground mt-1">退货全流程：申请→审核→出库→完成</p>
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
                            <div class="text-sm font-semibold text-foreground mb-3">采购退货状态流转</div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700">1 待审核</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-blue-100 text-blue-700">2 已审核</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-orange-100 text-orange-700">3 已出库</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-green-100 text-green-700">4 完成</span>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-[11px] text-muted-foreground">待审核/已审核</span>
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
                            <div class="text-sm font-semibold text-foreground mb-4">采购退货流程说明</div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700 shrink-0">待审核</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">提交退货申请后等待审核</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-blue-100 text-blue-700 shrink-0">已审核</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">审核通过，等待退货出库</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-orange-100 text-orange-700 shrink-0">已出库</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">退货商品已从仓库出库退回</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-green-100 text-green-700 shrink-0">完成</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">退货全流程结束，单据完结</span>
                                </div>
                                <div class="border-t border-border pt-3 mt-1">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-red-100 text-red-700 shrink-0">已作废</span>
                                        <span class="text-[11px] leading-snug text-muted-foreground">退货单中途作废失效；已出库状态不可作废</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @can('purchase.purchase-return.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增退货单
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索退货单号..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待审核</option>
            <option value="2">已审核</option>
            <option value="3">已出库</option>
            <option value="4">完成</option>
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

    {{-- 列表 --}}
    @php
        $visibleCols = collect($this->getAllColumns())->filter(fn($col) => $this->isColumnVisible($col['key']));
    @endphp
    <div class="rounded-lg border bg-card overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    @foreach($visibleCols as $col)
                        <th class="px-4 py-2 text-left">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="preturn-{{ $item->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    @foreach($visibleCols as $col)
                        @switch($col['key'])
                            @case('return_no')
                                <td class="px-4 py-2">
                                    <a href="{{ route('purchase-returns.detail', $item->id) }}" class="font-mono text-blue-600 hover:underline">{{ $item->return_no }}</a>
                                </td>
                                @break
                            @case('purchase_order_id')
                                <td class="px-4 py-2 text-foreground">{{ $item->purchaseOrder?->order_no ?? '-' }}</td>
                                @break
                            @case('supplier_id')
                                <td class="px-4 py-2 text-foreground">{{ $item->supplier?->name ?? '-' }}</td>
                                @break
                            @case('warehouse_id')
                                <td class="px-4 py-2 text-foreground">{{ $item->warehouse?->name ?? '-' }}</td>
                                @break
                            @case('status')
                                <td class="px-4 py-2">{!! status_badge($item->status, 'purchase_return') !!}</td>
                                @break
                            @case('total_amount')
                                <td class="px-4 py-2 text-foreground">{{ money_format($item->total_amount) }}</td>
                                @break
                            @case('actual_amount')
                                <td class="px-4 py-2 text-foreground">{{ money_format($item->actual_amount) }}</td>
                                @break
                            @case('created_at')
                                <td class="px-4 py-2 text-muted-foreground">{{ $item->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                @break
                            @default
                                <td class="px-4 py-2 text-foreground">{{ $item->{$col['key']} ?? '-' }}</td>
                        @endswitch
                    @endforeach
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-1">
                            {{-- 状态流转 --}}
                            @if($item->status === 1)
                                <button type="button" wire:click="approveReturn({{ $item->id }})" class="p-1 rounded text-green-600 hover:bg-green-50 hover:text-green-700 transition-colors" title="审核"><x-ui.icon name="check" class="w-3.5 h-3.5" /></button>
                            @elseif($item->status === 2)
                                <button type="button" wire:click="shipReturn({{ $item->id }})" class="p-1 rounded text-orange-600 hover:bg-orange-50 hover:text-orange-700 transition-colors" title="出库"><x-ui.icon name="truck" class="w-3.5 h-3.5" /></button>
                            @elseif($item->status === 3)
                                <button type="button" wire:click="completeReturn({{ $item->id }})" class="p-1 rounded text-green-600 hover:bg-green-50 hover:text-green-700 transition-colors" title="完成"><x-ui.icon name="check-circle" class="w-3.5 h-3.5" /></button>
                            @endif
                            @if(in_array($item->status, [1, 2]))
                                <button type="button" wire:click="cancelReturn({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="作废"><x-ui.icon name="x-circle" class="w-3.5 h-3.5" /></button>
                            @endif
                            {{-- 编辑 --}}
                            @can('purchase.purchase-return.edit')
                            @if(!in_array($item->status, [4, 9]))
                            <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endif
                            @endcan
                            @can('purchase.purchase-return.delete')
                            <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $visibleCols->count() + 2 }}" class="px-6 py-12 text-center text-muted-foreground">暂无退货单数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑退货单' : '新增退货单' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">关联采购单 <span class="text-red-500">*</span></label>
                    <select wire:model="formPurchaseOrderId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择采购单</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}">{{ $po->order_no }}</option>
                        @endforeach
                    </select>
                    @error('formPurchaseOrderId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">供应商 <span class="text-red-500">*</span></label>
                        <select wire:model="formSupplierId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('formSupplierId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">出库仓库 <span class="text-red-500">*</span></label>
                        <select wire:model="formWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                        @error('formWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">退货原因 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formReason" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入退货原因" />
                    @error('formReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
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

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
