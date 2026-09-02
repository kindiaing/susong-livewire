<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-foreground">损耗管理</h1>
                <p class="text-muted-foreground mt-1">损耗全流程：待处理→处理中→已出库/入库→已关闭</p>
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
                            <div class="text-sm font-semibold text-foreground mb-3">损耗单状态流转</div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700">1 待处理</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-blue-100 text-blue-700">2 处理中</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-green-100 text-green-700">3 已出库/已入库</span>
                                <x-ui.icon name="chevron-right" class="w-3.5 h-3.5 text-muted-foreground shrink-0" />
                                <span class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-medium leading-none bg-gray-100 text-gray-600">4 已关闭</span>
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
                         x-cloak x-ref="card"
                         @mouseenter="show = true; clearTimeout(timer)"
                         @mouseleave="show = false"
                         class="fixed z-50 w-96 rounded-lg border border-border bg-popover text-popover-foreground shadow-lg outline-none"
                         :style="'top:' + top + 'px;left:' + left + 'px'">
                        <div class="p-6">
                            <div class="text-sm font-semibold text-foreground mb-4">损耗单流程说明</div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-yellow-100 text-yellow-700 shrink-0">待处理</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">损耗单已提交，等待审核处理</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-blue-100 text-blue-700 shrink-0">处理中</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">审核通过，正在执行损耗出库或入库</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-green-100 text-green-700 shrink-0">已出库/入库</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">库存变动已完成</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-gray-100 text-gray-600 shrink-0">已关闭</span>
                                    <span class="text-[11px] leading-snug text-muted-foreground">损耗单已关闭完结</span>
                                </div>
                                <div class="border-t border-border pt-3 mt-1">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center rounded px-2.5 py-1 w-[60px] text-[11px] font-medium leading-none bg-red-100 text-red-700 shrink-0">已作废</span>
                                        <span class="text-[11px] leading-snug text-muted-foreground">损耗单中途作废失效</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @can('loss.loss-order.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增损耗单
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索损耗单号..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterLossType" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部类型</option>
            <option value="1">存储腐坏</option>
            <option value="2">称重失水</option>
            <option value="3">过期报废</option>
            <option value="4">加工损耗</option>
            <option value="5">盘点差异</option>
            <option value="6">其他</option>
            <option value="7">采购少收</option>
            <option value="8">拣货差异</option>
            <option value="9">配送差异</option>
        </select>
        <select wire:model.live="filterStatus" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待审核</option>
            <option value="2">已通过</option>
            <option value="3">已执行</option>
            <option value="4">已关闭</option>
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

    @php
        $allCols = collect($this->getAllColumns());
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']))->values();
        $colspan = $visibleCols->count() + 2;
    @endphp

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    @foreach($visibleCols as $col)
                    <th class="px-4 py-2 text-left">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-2 text-right w-24 action-col">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="loss-{{ $item->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <td class="px-4 py-2 text-muted-foreground">{{ $item->id }}</td>
                            @break
                        @case('loss_no')
                            <td class="px-4 py-2 font-medium"><a href="{{ route('loss-orders.detail', $item->id) }}" class="text-blue-600 hover:underline">{{ $item->loss_no }}</a></td>
                            @break
                        @case('warehouse_id')
                            <td class="px-4 py-2 text-foreground">{{ $item->warehouse?->name ?? '-' }}</td>
                            @break
                        @case('loss_type')
                            <td class="px-4 py-2 text-foreground">{{ \App\Models\LossOrder::typeMap()[$item->loss_type] ?? '-' }}</td>
                            @break
                        @case('total_amount')
                            <td class="px-4 py-2 text-foreground text-right">{{ money_format($item->total_amount) }}</td>
                            @break
                        @case('status')
                            <td class="px-4 py-2">{!! status_badge($item->status, 'loss_status') !!}</td>
                            @break
                        @case('approval_status')
                            <td class="px-4 py-2">{!! status_badge($item->approval_status, 'loss_approval') !!}</td>
                            @break
                        @case('reason')
                            <td class="px-4 py-2 text-muted-foreground truncate">{{ $item->reason ?? '-' }}</td>
                            @break
                        @case('created_at')
                            <td class="px-4 py-2 text-muted-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                            @break
                        @default
                            <td class="px-4 py-2 text-foreground">{{ $item->{$col['key']} ?? '-' }}</td>
                    @endswitch
                    @endforeach
                    <td class="px-4 py-2 action-col">
                        <div class="flex items-center gap-0.5">
                            @can('loss.loss-order.edit')
                            @if($item->approval_status === 1)
                                <button type="button" wire:click="confirmApprove({{ $item->id }})" class="p-1 rounded text-green-600 hover:bg-green-50 hover:text-green-700 transition-colors" title="审核"><x-ui.icon name="check" class="w-3.5 h-3.5" /></button>
                            @endif
                            @if($item->status === 2 && $item->source_type !== 'purchase_order')
                                <button type="button" wire:click="execute({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="执行"><x-ui.icon name="bolt" class="w-3.5 h-3.5" /></button>
                            @endif
                            @if($item->status === 3 || ($item->status === 2 && $item->source_type === 'purchase_order'))
                                <button type="button" wire:click="confirmClose({{ $item->id }})" class="p-1 rounded text-orange-600 hover:bg-orange-50 hover:text-orange-700 transition-colors" title="{{ $item->source_type === 'purchase_order' && $item->status === 2 ? '采购入库差异仅账务留痕不扣库存，审核后可直接关闭' : '关闭' }}"><x-ui.icon name="x-circle" class="w-3.5 h-3.5" /></button>
                            @endif
                            @if($item->canEdit())
                                <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endif
                            @endcan
                            @can('loss.loss-order.delete')
                            @if($item->canEdit())
                            <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $colspan }}" class="px-6 py-12 text-center text-muted-foreground">暂无损耗单数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-3xl mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑损耗单' : '新增损耗单' }}
                    @if($editingStatusLabel)
                    <span class="ml-2 inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-{{ $editingStatusColor }}-100 text-{{ $editingStatusColor }}-700">{{ $editingStatusLabel }}</span>
                    @endif
                </h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">仓库 <span class="text-red-500">*</span></label>
                        <select wire:model="formWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择仓库</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                        @error('formWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">损耗类型 <span class="text-red-500">*</span></label>
                        <select wire:model="formLossType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">存储腐坏</option>
                            <option value="2">称重失水</option>
                            <option value="3">过期报废</option>
                            <option value="4">加工损耗</option>
                            <option value="5">盘点差异</option>
                            <option value="6">其他</option>
                        </select>
                        @error('formLossType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">损耗原因</label>
                        <input type="text" wire:model="formReason" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                        @error('formReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                        <input type="text" wire:model="formRemark" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                        @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 损耗明细行 --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-foreground">损耗明细 <span class="text-red-500">*</span></label>
                        <button type="button" wire:click="addItemRow" class="rounded-md border border-input px-2 py-1 text-xs hover:bg-accent transition-colors">+ 添加行</button>
                    </div>
                    @if(count($formItems) > 0)
                    <div class="overflow-x-auto rounded-md border border-input">
                        <table class="w-full text-sm min-w-[760px]">
                            <thead>
                                <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                                    <th class="px-3 py-1.5 text-left">SKU</th>
                                    <th class="px-3 py-1.5 text-right w-[100px]">成本价（元）</th>
                                    <th class="px-3 py-1.5 text-right w-[90px]">数量</th>
                                    <th class="px-3 py-1.5 text-right w-[100px]">损耗金额</th>
                                    <th class="px-3 py-1.5 text-left w-[100px]">责任方</th>
                                    <th class="px-3 py-1.5 text-left w-[120px]">供应商</th>
                                    <th class="px-3 py-1.5 text-left w-[120px]">原因</th>
                                    <th class="px-3 py-1.5 text-center w-[40px]"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($formItems as $i => $row)
                                <tr wire:key="loss-item-{{ $i }}">
                                    <td class="px-3 py-1.5">
                                        <select wire:model="formItems.{{ $i }}.sku_id" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs">
                                            <option value="0">选择 SKU</option>
                                            @foreach($skus as $sku)
                                            <option value="{{ $sku->id }}">{{ $sku->product?->name ?? 'SKU' }}（{{ $sku->sku_code }}）</option>
                                            @endforeach
                                        </select>
                                        @error("formItems.{$i}.sku_id") <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-3 py-1.5">
                                        <input type="text" wire:model="formItems.{{ $i }}.cost_price_yuan" placeholder="0.00" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs text-right" />
                                        @error("formItems.{$i}.cost_price_yuan") <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-3 py-1.5">
                                        <input type="number" wire:model="formItems.{{ $i }}.quantity" min="1" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs text-right" />
                                        @error("formItems.{$i}.quantity") <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-3 py-1.5 text-right text-muted-foreground tabular-nums">
                                        @php
                                            $rowQty = (int) ($row['quantity'] ?? 0);
                                            $rowPrice = (float) ($row['cost_price_yuan'] ?? 0);
                                            $rowAmount = $rowQty * $rowPrice;
                                        @endphp
                                        {{ number_format($rowAmount, 2) }}
                                    </td>
                                    <td class="px-3 py-1.5">
                                        <select wire:model="formItems.{{ $i }}.responsible_party" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs">
                                            <option value="1">平台</option>
                                            <option value="2">供应商</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-1.5">
                                        @if(($row['responsible_party'] ?? 1) == 2)
                                        <select wire:model="formItems.{{ $i }}.supplier_id" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs">
                                            <option value="0">选择供应商</option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <span class="text-xs text-muted-foreground">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-1.5">
                                        <input type="text" wire:model="formItems.{{ $i }}.reason" placeholder="可选" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs" />
                                    </td>
                                    <td class="px-3 py-1.5 text-center">
                                        <button type="button" wire:click="removeItemRow({{ $i }})" class="text-red-500 hover:text-red-700" title="删除行"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2">
                                    <td colspan="3" class="px-3 py-1.5 text-right text-xs text-muted-foreground">合计</td>
                                    <td class="px-3 py-1.5 text-right text-xs font-bold text-foreground tabular-nums">{{ money_format($formTotalAmount) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">选择 SKU 后自动带出成本价（加权平均成本价，为 0 时取采购价），可手动修改。损耗金额 = 成本价 × 数量，自动计算。</p>
                    @else
                    <div class="rounded-md border border-dashed border-input px-4 py-8 text-center text-sm text-muted-foreground">
                        暂无明细。点击右上角「+ 添加行」录入损耗商品。
                    </div>
                    @endif
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
    @if($showApproveConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">审核损耗单</h2>
                <button type="button" wire:click="closeApproveConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">请选择审核结果</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeApproveConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="reject" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">拒绝</button>
                <button type="button" wire:click="approve" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">通过</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 关闭确认弹窗 --}}
    @if($showCloseConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认关闭损耗单</h2>
                <button type="button" wire:click="closeCloseConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">关闭后单据将无法再进行任何操作（不可编辑、执行或回退状态），请确认是否继续？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeCloseConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">再想想</button>
                <button type="button" wire:click="close({{ $closingId }})" class="rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 transition-colors">确认关闭</button>
            </div>
        </div>
    </div>
    @endif
</div>
