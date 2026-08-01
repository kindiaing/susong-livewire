<div class="p-6">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('purchase-orders') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $order->order_no }}</h1>
            @php $sc = ['1'=>'yellow','2'=>'blue','3'=>'orange','4'=>'green','5'=>'green','9'=>'gray']; $c = $sc[$order->status] ?? 'gray'; @endphp
            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ $order->status_label }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if($order->status === 1)
                <button type="button" wire:click="confirmSubmit" type="button" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">提交</button>
            @elseif($order->status === 2)
                <button type="button" wire:click="confirmShip" type="button" class="rounded-md bg-orange-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-700 transition-colors">发货</button>
            @elseif($order->status === 3)
                <button type="button" wire:click="openStockInModal" type="button" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">入库</button>
            @elseif($order->status === 4)
                <button type="button" wire:click="confirmComplete" type="button" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">完成</button>
            @endif

            @if(in_array($order->status, [1, 2, 3]))
                <button type="button" wire:click="confirmCancel" type="button" class="rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">取消</button>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息：一行横排 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-4">
        <span>供应商：<b class="text-foreground">{{ $order->supplier?->name ?? '-' }}</b></span>
        @if($order->warehouse)<span>仓库：<b class="text-foreground">{{ $order->warehouse->name }}</b></span>@endif
        @if($order->operator)<span>经办人：<b class="text-foreground">{{ $order->operator->name }}</b></span>@endif
        <span>总金额：<b class="text-foreground">{{ money_format($order->total_amount) }}</b></span>
        <span>实际金额：<b class="text-foreground">{{ money_format($order->actual_amount) }}</b></span>
        <span>下单：<b class="text-foreground">{{ $order->ordered_at?->format('Y-m-d H:i') ?? '-' }}</b></span>
        <span>入库：<b class="text-foreground">{{ $order->stocked_at?->format('Y-m-d H:i') ?? '-' }}</b></span>
        @if($order->remark)<span>备注：<b class="text-foreground">{{ $order->remark }}</b></span>@endif
    </div>

    {{-- 明细区：工具栏 + 表格 --}}
    <div class="rounded-lg border bg-card">
        <div class="flex items-center justify-between px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">采购明细</h2>
            <div class="flex items-center gap-2">
                @if(in_array($order->status, [1, 2]))
                    <button type="button" wire:click="openImportModal" type="button" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                        <x-ui.icon name="arrow-up-tray" class="w-3.5 h-3.5" /> 导入
                    </button>
                @endif
                <button type="button" wire:click="openExportModal" type="button" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                    <x-ui.icon name="arrow-down-tray" class="w-3.5 h-3.5" /> 导出
                </button>
                @if(in_array($order->status, [1, 2]))
                    <button type="button" wire:click="openAddItemModal" type="button" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-3.5 h-3.5" /> 添加
                    </button>
                @endif
            </div>
        </div>

        {{-- 表头 + 表体 --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                    <th class="px-3 py-1.5 text-left">SKU</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">单价</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">金额</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">实际数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">实际金额</th>
                    <th class="px-3 py-1.5 w-[30px]"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="item-{{ $item->id }}">
                    <td class="px-3 py-1.5 truncate">
                        <span class="font-mono text-foreground">{{ $item->sku?->sku_code }}</span>
                        <span class="text-muted-foreground ml-1">{{ $item->sku?->product?->name }}</span>
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->quantity }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ money_format($item->price) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums font-medium">{{ money_format($item->amount) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums {{ $item->actual_quantity && $item->actual_quantity != $item->quantity ? 'text-orange-600 font-medium' : '' }}">{{ $item->actual_quantity ?: '-' }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->actual_amount ? money_format($item->actual_amount) : '-' }}</td>
                    <td class="px-3 py-1.5">
                        @if(in_array($order->status, [1, 2]))
                            <button type="button" wire:click="removeItem({{ $item->id }})" type="button" class="text-red-500 hover:text-red-700">
                                <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                            </button>
                        @endif
                    </td>
                </tr>
                @if($item->discrepancy_reason)
                <tr>
                    <td colspan="7" class="px-3 py-0.5 bg-orange-50 text-[11px] text-orange-700">
                        差异：{{ $item->discrepancy_reason }}
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="7" class="px-3 py-6 text-center text-muted-foreground">暂无明细</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 导出弹窗 --}}
    @if($showExportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeExportModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">导出采购明细</h2>
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
                <button type="button" wire:click="closeExportModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeExport" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">导出 Excel</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 导入弹窗 --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeImportModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">导入采购明细</h2>
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
                <button type="button" wire:click="closeImportModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeImport" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">导入</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 添加明细弹窗 --}}
    @if($showAddItemModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeAddItemModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">添加采购明细</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">SKU <span class="text-red-500">*</span></label>
                    <select wire:model="formSkuId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择 SKU</option>
                        @foreach($skus as $s)
                            <option value="{{ $s->id }}">{{ $s->sku_code }} - {{ $s->product?->name }}</option>
                        @endforeach
                    </select>
                    @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">数量 <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="formQuantity" min="1" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formQuantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">采购单价（元） <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="formPrice" min="0" step="0.01" placeholder="0.00" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeAddItemModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="saveItem" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">添加</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 入库弹窗 --}}
    @if($showStockInModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeStockInModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-3xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">采购入库</h2>
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
                <button type="button" wire:click="closeStockInModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeStockIn" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认入库</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 状态确认弹窗 --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeConfirmModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认操作</h2>
            <p class="text-sm text-muted-foreground mb-6">{{ $confirmTitle }}</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeConfirmModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeConfirm" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认</button>
            </div>
        </div>
    </div>
    @endif
</div>
