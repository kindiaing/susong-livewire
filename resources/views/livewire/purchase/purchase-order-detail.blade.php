<div class="p-6">
    {{-- 返回 + 标题 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('purchase-orders') }}" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-foreground">采购单 {{ $order->order_no }}</h1>
                <p class="text-muted-foreground mt-1">
                    供应商：{{ $order->supplier?->name ?? '-' }}
                    @if($order->warehouse) ｜ 入库仓库：{{ $order->warehouse->name }} @endif
                    @if($order->operator) ｜ 经办人：{{ $order->operator->name }} @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php $sc = ['1'=>'yellow','2'=>'blue','3'=>'orange','4'=>'green','5'=>'green','9'=>'gray']; $c = $sc[$order->status] ?? 'gray'; @endphp
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-{{ $c }}-100 text-{{ $c }}-700">
                {{ $order->status_label }}
            </span>

            @if($order->status === 1)
                <button wire:click="confirmSubmit" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">提交</button>
            @elseif($order->status === 2)
                <button wire:click="confirmShip" type="button" class="rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 transition-colors">发货</button>
            @elseif($order->status === 3)
                <button wire:click="openStockInModal" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">入库</button>
            @elseif($order->status === 4)
                <button wire:click="confirmComplete" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">完成</button>
            @endif

            @if(in_array($order->status, [1, 2, 3]))
                <button wire:click="confirmCancel" type="button" class="rounded-md border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">取消</button>
            @endif
        </div>
    </div>

    {{-- 基本信息 --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="rounded-lg border bg-card p-4">
            <p class="text-xs text-muted-foreground">总金额</p>
            <p class="text-xl font-bold text-foreground mt-1">{{ money_format($order->total_amount) }}</p>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <p class="text-xs text-muted-foreground">实际入库金额</p>
            <p class="text-xl font-bold text-foreground mt-1">{{ money_format($order->actual_amount) }}</p>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <p class="text-xs text-muted-foreground">下单时间</p>
            <p class="text-sm text-foreground mt-1">{{ $order->ordered_at?->format('Y-m-d H:i') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <p class="text-xs text-muted-foreground">入库时间</p>
            <p class="text-sm text-foreground mt-1">{{ $order->stocked_at?->format('Y-m-d H:i') ?? '-' }}</p>
        </div>
    </div>

    {{-- 明细列表 --}}
    <div class="rounded-lg border bg-card">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-foreground">采购明细</h2>
            @if(in_array($order->status, [1, 2]))
                <button wire:click="openAddItemModal" type="button" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">添加明细</button>
            @endif
        </div>
        <div class="grid grid-cols-[1fr_100px_100px_100px_100px_100px_40px] gap-3 px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider border-b">
            <div>SKU</div><div>采购数量</div><div>采购单价</div><div>采购金额</div><div>实际数量</div><div>实际金额</div><div></div>
        </div>
        @forelse($order->items as $item)
            <div class="grid grid-cols-[1fr_100px_100px_100px_100px_100px_40px] gap-3 px-6 py-3 items-center border-b last:border-b-0 hover:bg-muted/30 transition-colors">
                <div class="text-sm">
                    <span class="font-mono text-foreground">{{ $item->sku?->sku_code }}</span>
                    <span class="text-muted-foreground ml-2">{{ $item->sku?->product?->name }}</span>
                </div>
                <div class="text-sm">{{ $item->quantity }}</div>
                <div class="text-sm">{{ money_format($item->price) }}</div>
                <div class="text-sm font-medium">{{ money_format($item->amount) }}</div>
                <div class="text-sm {{ $item->actual_quantity && $item->actual_quantity != $item->quantity ? 'text-orange-600 font-medium' : '' }}">{{ $item->actual_quantity ?: '-' }}</div>
                <div class="text-sm">{{ $item->actual_amount ? money_format($item->actual_amount) : '-' }}</div>
                <div>
                    @if(in_array($order->status, [1, 2]))
                        <button wire:click="removeItem({{ $item->id }})" type="button" class="text-red-500 hover:text-red-700">
                            <x-ui.icon name="trash" class="w-4 h-4" />
                        </button>
                    @endif
                </div>
            </div>
            @if($item->discrepancy_reason)
                <div class="px-6 py-2 bg-orange-50 text-xs text-orange-700 border-b">
                    差异原因：{{ $item->discrepancy_reason }}
                </div>
            @endif
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无明细</div>
        @endforelse
    </div>

    {{-- 备注 --}}
    @if($order->remark)
        <div class="mt-4 rounded-lg border bg-card p-4">
            <p class="text-xs text-muted-foreground mb-1">备注</p>
            <p class="text-sm text-foreground">{{ $order->remark }}</p>
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
                    <label class="block text-sm font-medium text-foreground mb-1">采购单价(厘) <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="formPrice" min="0" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="closeAddItemModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="saveItem" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">添加</button>
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
                        <div>SKU</div><div>采购数</div><div>实际数量</div><div>实际单价(厘)</div><div>差异</div><div>差异原因</div>
                    </div>
                    @foreach($stockInItems as $i => $item)
                        <div class="grid grid-cols-[1fr_80px_80px_80px_80px_1fr] gap-2 px-4 py-2 items-center border-b last:border-b-0 text-sm">
                            <div class="text-foreground truncate">{{ $item['sku_name'] }}</div>
                            <div class="text-muted-foreground">{{ $item['quantity'] }}</div>
                            <div>
                                <input type="number" wire:model="stockInItems.{{ $i }}.actual_quantity" min="0" class="w-full h-7 rounded border border-input bg-background px-2 text-sm" />
                            </div>
                            <div>
                                <input type="number" wire:model="stockInItems.{{ $i }}.actual_price" min="0" class="w-full h-7 rounded border border-input bg-background px-2 text-sm" />
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
                <button wire:click="closeStockInModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="executeStockIn" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认入库</button>
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
                <button wire:click="closeConfirmModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="executeConfirm" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认</button>
            </div>
        </div>
    </div>
    @endif
</div>
