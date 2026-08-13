<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('purchase-orders') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $order->order_no }}</h1>
            {!! status_badge($order->status, 'purchase_order') !!}
        </div>
        <div class="flex items-center gap-2">
            @if($order->status === 1)
                <button type="button" wire:click="confirmSubmit" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">提交</button>
            @elseif($order->status === 2)
                <button type="button" wire:click="confirmShip" class="rounded-md bg-orange-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-700 transition-colors">发货</button>
            @elseif($order->status === 3)
                <button type="button" wire:click="openStockInModal" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">入库</button>
            @elseif($order->status === 4)
                <button type="button" wire:click="confirmComplete" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">完成</button>
            @endif

            @if(in_array($order->status, [1, 2, 3]))
                <button type="button" wire:click="confirmCancel" class="rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">作废</button>
            @endif

            {{-- 超管状态回退 --}}
            @if($isSuperAdmin && !in_array($order->status, [1, 9]))
                <div class="relative" x-data="{open: false}">
                    <button type="button" @click="open = !open" class="rounded-md border border-purple-300 px-3 py-1.5 text-sm font-medium text-purple-600 hover:bg-purple-50 transition-colors">回退</button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-36 rounded-md border bg-background shadow-lg z-10 py-1">
                        @if($order->status > 1)<button type="button" wire:click="confirmRollback(1)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到待接单</button>@endif
                        @if($order->status > 2)<button type="button" wire:click="confirmRollback(2)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到备货中</button>@endif
                        @if($order->status > 3)<button type="button" wire:click="confirmRollback(3)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到已发货</button>@endif
                        @if($order->status > 4)<button type="button" wire:click="confirmRollback(4)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到已入库</button>@endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息：一行横排 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-4">
        <span>供应商：<b class="text-foreground">{{ $order->supplier?->name ?? '-' }}</b></span>
        @if($order->purchase_date)<span>采购日期：<b class="text-foreground">{{ $order->purchase_date->format('Y-m-d') }}</b></span>@endif
        @if($order->warehouse)<span>仓库：<b class="text-foreground">{{ $order->warehouse->name }}</b></span>@endif
        @if($order->operator)<span>经办人：<b class="text-foreground">{{ $order->operator->name }}</b></span>@endif
        <span>总金额：<b class="text-foreground">{{ money_format($order->total_amount) }}</b></span>
        <span>实际金额：<b class="text-foreground">{{ money_format($order->actual_amount) }}</b></span>
        <span>下单：<b class="text-foreground">{{ $order->ordered_at?->format('Y-m-d H:i') ?? '-' }}</b></span>
        <span>入库：<b class="text-foreground">{{ $order->stocked_at?->format('Y-m-d H:i') ?? '-' }}</b></span>
        @if($order->cancelled_at)<span>作废：<b class="text-foreground">{{ $order->cancelled_at->format('Y-m-d H:i') }}</b></span>@endif
        @if($order->cancel_reason)<span>作废原因：<b class="text-foreground">{{ $order->cancel_reason }}</b></span>@endif
        @if($order->remark)<span>备注：<b class="text-foreground">{{ $order->remark }}</b></span>@endif
    </div>

    {{-- 明细区 + 状态变更记录：Tab 切换 --}}
    <div class="rounded-lg border bg-card" x-data="{ activeTab: 'items' }">
        {{-- Tab 导航 + 工具栏 --}}
        <div class="flex items-center border-b">
            <button type="button" @click="activeTab = 'items'" :class="activeTab === 'items' ? 'border-b-2 border-blue-600 text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'" class="px-4 py-2 text-sm transition-colors">
                采购明细
            </button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-b-2 border-blue-600 text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'" class="px-4 py-2 text-sm transition-colors">
                状态变更记录
                @if($order->auditLogs->isNotEmpty())
                    <span class="ml-1 inline-flex items-center justify-center rounded-full bg-blue-100 text-blue-700 px-1.5 py-0.5 text-[10px] font-medium leading-none">{{ $order->auditLogs->count() }}</span>
                @endif
            </button>
            <div class="ml-auto flex items-center gap-2 pr-4" x-show="activeTab === 'items'">
                @if(in_array($order->status, [1, 2]) || $isSuperAdmin && !in_array($order->status, [9]))
                    <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                        <x-ui.icon name="arrow-down-tray" class="w-3.5 h-3.5" /> 导入
                    </button>
                @endif
                <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                    <x-ui.icon name="arrow-up-tray" class="w-3.5 h-3.5" /> 导出
                </button>
                @if(in_array($order->status, [1, 2]) || $isSuperAdmin && !in_array($order->status, [9]))
                    <button type="button" wire:click="openAddItemModal" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-3.5 h-3.5" /> 添加
                    </button>
                @endif
            </div>
        </div>

        {{-- 采购明细 Tab --}}
        <div x-show="activeTab === 'items'">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                    <th class="px-3 py-1.5 text-left">SKU</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">单价</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">改价</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">金额</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">实际数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">实际金额</th>
                    <th class="px-3 py-1.5 w-[60px]"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="item-{{ $item->id }}">
                    <td class="px-3 py-1.5 truncate">
                        <span class="font-mono text-foreground">{{ $item->sku?->sku_code }}</span>
                        <span class="text-muted-foreground ml-1">{{ $item->sku?->product?->name }}</span>
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">
                        @php
                            $qtyDisplay = (string)$item->quantity;
                            if ($item->sku && $item->sku->base_unit_id && $item->unit_id && $item->unit_quantity) {
                                try {
                                    $svc = app(\App\Services\UnitConversionService::class);
                                    $qtyDisplay = $svc->formatWithConversion($item->sku_id, $item->unit_id, $item->unit_quantity);
                                } catch (\Exception $e) {
                                    $qtyDisplay = (string)$item->quantity;
                                }
                            } elseif ($item->sku && $item->sku->base_unit_id) {
                                try {
                                    $svc = app(\App\Services\UnitConversionService::class);
                                    $qtyDisplay = $svc->formatHuman($item->sku_id, $item->quantity);
                                } catch (\Exception $e) {}
                            }
                        @endphp
                        {{ $qtyDisplay }}
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ money_format($item->price) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums {{ $item->strategy_price ? 'text-blue-600 font-medium' : 'text-muted-foreground' }}">{{ $item->strategy_price ? money_format($item->strategy_price) : '-' }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums font-medium">{{ money_format($item->amount) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums {{ $item->actual_quantity && $item->actual_quantity != $item->quantity ? 'text-orange-600 font-medium' : '' }}">{{ $item->actual_quantity ?: '-' }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->actual_amount ? money_format($item->actual_amount) : '-' }}</td>
                    <td class="px-3 py-1.5">
                        <div class="flex items-center gap-1">
                            @if(in_array($order->status, [1, 2]) || $isSuperAdmin && !in_array($order->status, [9]))
                                <button type="button" wire:click="openEditItemModal({{ $item->id }})" class="text-blue-500 hover:text-blue-700" title="编辑">
                                    <x-ui.icon name="pencil-square" class="w-3.5 h-3.5" />
                                </button>
                                <button type="button" wire:click="confirmDeleteItem({{ $item->id }})" class="text-red-500 hover:text-red-700" title="删除">
                                    <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @if($item->discrepancy_reason)
                <tr>
                    <td colspan="8" class="px-3 py-0.5 bg-orange-50 text-[11px] text-orange-700">
                        差异：{{ $item->discrepancy_reason }}
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="8" class="px-3 py-6 text-center text-muted-foreground">暂无明细</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- 状态变更记录 Tab --}}
        <div x-show="activeTab === 'logs'">
            @if($order->auditLogs->isNotEmpty())
            <div class="divide-y">
                @foreach($order->auditLogs->sortByDesc('created_at') as $log)
                <div class="flex items-center gap-3 px-4 py-2 text-sm">
                    <span class="text-muted-foreground text-xs w-32 shrink-0">{{ $log->created_at?->format('Y-m-d H:i:s') }}</span>
                    <span class="font-medium text-foreground">{{ $log->action_label }}</span>
                    @if($log->before_data && isset($log->before_data['status_label']))
                        <span class="text-muted-foreground">{{ $log->before_data['status_label'] }}</span>
                        <x-ui.icon name="arrow-right" class="w-3 h-3 text-muted-foreground" />
                        <span class="text-foreground font-medium">{{ $log->after_data['status_label'] ?? '-' }}</span>
                    @endif
                    @if($log->operator)
                        <span class="text-muted-foreground ml-auto">操作人：{{ $log->operator->name }}</span>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="px-4 py-8 text-center text-muted-foreground text-sm">暂无状态变更记录</div>
            @endif
        </div>
    </div>

    {{-- 导出弹窗 --}}
    @if($showExportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">导出采购明细</h2>
                <button type="button" wire:click="closeExportModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-sku" checked disabled class="rounded border-input" />
                    <label for="exp-sku" class="text-sm">SKU</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-qty" checked disabled class="rounded border-input" />
                    <label for="exp-qty" class="text-sm">采购数量</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-price" checked disabled class="rounded border-input" />
                    <label for="exp-price" class="text-sm">采购单价</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-amount" checked disabled class="rounded border-input" />
                    <label for="exp-amount" class="text-sm">采购金额</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-actual" wire:model="exportActual" class="rounded border-input" />
                    <label for="exp-actual" class="text-sm">实际入库数量/金额</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-discrepancy" wire:model="exportDiscrepancy" class="rounded border-input" />
                    <label for="exp-discrepancy" class="text-sm">差异原因</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeExportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeExport" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">导出 Excel</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 导入弹窗 --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">导入采购明细</h2>
                <button type="button" wire:click="closeImportModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">选择 Excel 文件</label>
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-muted-foreground
                        file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium
                        file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    @error('importFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-md bg-muted p-3 text-xs text-muted-foreground">
                    <p class="font-medium mb-1">导入模板列：</p>
                    <p>SKU编码 | 数量 | 采购单价（元）</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeImportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeImport" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">导入</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 添加/编辑明细弹窗 --}}
    @if($showAddItemModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingItemId ? '编辑采购明细' : '添加采购明细' }}</h2>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="closeAddItemModal" class="text-muted-foreground hover:text-foreground transition-colors">
                        <x-ui.icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">SKU <span class="text-red-500">*</span></label>
                    <select wire:model.live="formSkuId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择 SKU</option>
                        @foreach($skus as $s)
                            <option value="{{ $s->id }}">{{ $s->sku_code }} - {{ $s->product?->name }}</option>
                        @endforeach
                    </select>
                    @php
                        $selectedSku = null;
                        if ($formSkuId > 0) {
                            foreach ($skus as $s) {
                                if ($s->id == $formSkuId) {
                                    $selectedSku = $s;
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($selectedSku && $selectedSku->base_unit_id && $availableUnits)
                        <p class="text-xs text-muted-foreground mt-1">该 SKU 已配置单位换算（基础单位：{{ $selectedSku->baseUnit?->name ?? '-' }}）</p>
                    @elseif($selectedSku && !$selectedSku->base_unit_id)
                        <p class="text-xs text-muted-foreground mt-1">该 SKU 未配置单位换算，直接输入数量即可</p>
                    @endif
                    @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">数量 <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="number" wire:model.live="formUnitQuantity" min="1" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0" />
                        @if($availableUnits)
                        <select wire:model.live="formUnitId" class="flex h-9 w-28 rounded-md border border-input bg-background px-2 text-sm">
                            <option value="">单位</option>
                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit['unit_id'] }}">{{ $unit['unit_name'] }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    @if($unitPreview)
                        <p class="text-xs text-blue-600 mt-1">换算：{{ $unitPreview }}</p>
                    @endif
                    @if($formUnitId && $formUnitQuantity > 0 && $formQuantity > 0)
                        <p class="text-xs text-muted-foreground mt-1">基础数量：{{ $formQuantity }}</p>
                    @endif
                    @error('formQuantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">采购单价（元） <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="formPrice" min="0" step="0.01" placeholder="0.00" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">改价/促销单价（元）</label>
                    <input type="number" wire:model="formStrategyPrice" min="0" step="0.01" placeholder="可选，留空表示无改价" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formStrategyPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <input type="text" wire:model="formRemark" placeholder="可选" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeAddItemModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="saveItem" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingItemId ? '保存' : '添加' }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 入库弹窗 --}}
    @if($showStockInModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-3xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">采购入库</h2>
                <button type="button" wire:click="closeStockInModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">入库仓库 <span class="text-red-500">*</span></label>
                        <select wire:model="stockInWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择仓库</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                        @error('stockInWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">批次号 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="stockInBatchNo" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('stockInBatchNo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 逐行入库明细 --}}
                <div class="rounded-lg border">
                    <div class="grid grid-cols-[1fr_80px_80px_80px_80px_1fr] gap-2 px-4 py-2 text-xs font-medium text-muted-foreground border-b">
                        <div>SKU</div><div class="text-right">采购数</div><div>实际数量</div>                        <div>实际单价（元）</div><div class="text-right">差异</div><div>差异原因</div>
                    </div>
                    @foreach($stockInItems as $i => $item)
                        <div class="grid grid-cols-[1fr_80px_80px_80px_80px_1fr] gap-2 px-4 py-2 items-center border-b last:border-b-0 text-sm">
                            <div class="text-foreground truncate">{{ $item['sku_name'] }}</div>
                            <div class="text-muted-foreground text-right">{{ $item['quantity'] }}</div>
                            <div>
                                <input type="number" wire:model="stockInItems.{{ $i }}.actual_quantity" min="0" class="w-full h-7 rounded border border-input bg-background px-2 text-sm" />
                            </div>
                            <div>
                                <input type="number" wire:model="stockInItems.{{ $i }}.actual_price" min="0" step="0.01" placeholder="0.00" class="w-full h-7 rounded border border-input bg-background px-2 text-sm" />
                            </div>
                            <div class="text-center @if($item['actual_quantity'] != $item['quantity']) text-orange-600 font-medium @else text-green-600 @endif">
                                {{ $item['actual_quantity'] - $item['quantity'] }}
                            </div>
                            <div>
                                <input type="text" wire:model="stockInItems.{{ $i }}.discrepancy_reason" placeholder="可选" class="w-full h-7 rounded border border-input bg-background px-2 text-sm" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeStockInModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeStockIn" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认入库</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除明细确认弹窗 --}}
    @if($showDeleteItemConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认删除</h2>
                <button type="button" wire:click="closeDeleteItemConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">确认删除此采购明细？此操作不可撤销。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteItemConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="deleteItem" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 提交后返回列表页选择弹窗 --}}
    @if($showPostSubmitChoice)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">提交成功</h2>
                <button type="button" wire:click="closeConfirmModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">采购单已提交，是否返回列表页？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="stayOnDetail" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">留在当前页</button>
                <button type="button" wire:click="goBackToList" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">返回列表</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 状态确认弹窗 --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认操作</h2>
                <button type="button" wire:click="closeConfirmModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">{{ $confirmTitle }}</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeConfirmModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeConfirm" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认</button>
            </div>
        </div>
    </div>
    @endif
</div>
