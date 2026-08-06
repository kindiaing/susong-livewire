<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $order->order_no }}</h1>
            {!! status_badge($order->status, 'order') !!}
        </div>
        <div class="flex items-center gap-2">
            @if($order->status === 1)
                <button type="button" wire:click="confirmSubmit" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">提交</button>
            @elseif($order->status === 2)
                <button type="button" wire:click="confirmDeliver" class="rounded-md bg-orange-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-700 transition-colors">配送</button>
            @elseif($order->status === 3)
                <button type="button" wire:click="confirmSign" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">签收</button>
            @endif

            @if(!in_array($order->status, [4, 5, 9]))
                <button type="button" wire:click="confirmCancel" class="rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">取消</button>
            @endif

            {{-- 超管状态回退 --}}
            @if($isSuperAdmin && !in_array($order->status, [1, 9]))
                <div class="relative" x-data="{open: false}">
                    <button type="button" @click="open = !open" class="rounded-md border border-purple-300 px-3 py-1.5 text-sm font-medium text-purple-600 hover:bg-purple-50 transition-colors">回退</button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-36 rounded-md border bg-background shadow-lg z-10 py-1">
                        @if($order->status > 1)<button type="button" wire:click="confirmRollback(1)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到待拣货</button>@endif
                        @if($order->status > 2)<button type="button" wire:click="confirmRollback(2)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到拣货中</button>@endif
                        @if($order->status > 3)<button type="button" wire:click="confirmRollback(3)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到配送中</button>@endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息：一行横排 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-4">
        <span>商家：<b class="text-foreground">{{ $order->merchant?->name ?? '-' }}</b></span>
        @if($order->order_date)<span>单据日期：<b class="text-foreground">{{ $order->order_date->format('Y-m-d') }}</b></span>@endif
        @if($order->delivery_date)<span>收货日期：<b class="text-foreground">{{ $order->delivery_date->format('Y-m-d') }}</b></span>@endif
        @if($order->deliveryRoute)<span>配送线路：<b class="text-foreground">{{ $order->deliveryRoute->name }}</b></span>@endif
        @if($order->batch)<span>批次：<b class="text-foreground">{{ $order->batch_label }}</b></span>@endif
        <span>总金额：<b class="text-foreground">{{ money_format($order->total_amount) }}</b></span>
        <span>调整后：<b class="text-foreground">{{ money_format($order->adjusted_amount) }}</b></span>
        <span>结算金额：<b class="text-foreground">{{ money_format($order->final_amount) }}</b></span>
        @if($order->contact_name)<span>联系人：<b class="text-foreground">{{ $order->contact_name }}</b></span>@endif
        @if($order->contact_phone)<span>电话：<b class="text-foreground">{{ $order->contact_phone }}</b></span>@endif
        @if($order->remark)<span>备注：<b class="text-foreground">{{ $order->remark }}</b></span>@endif
    </div>

    {{-- 明细区：工具栏 + 表格 --}}
    <div class="rounded-lg border bg-card">
        <div class="flex items-center justify-between px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">订单明细</h2>
            <div class="flex items-center gap-2">
                @if($canEditItems)
                    <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                        <x-ui.icon name="arrow-up-tray" class="w-3.5 h-3.5" /> 导入
                    </button>
                @endif
                <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                    <x-ui.icon name="arrow-down-tray" class="w-3.5 h-3.5" /> 导出
                </button>
                @if($canEditItems)
                    <button type="button" wire:click="openAddItemModal" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-3.5 h-3.5" /> 添加
                    </button>
                @endif
            </div>
        </div>

        {{-- 表头 + 表体 --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                    <th class="px-3 py-1.5 text-left">商品</th>
                    <th class="px-3 py-1.5 text-left">规格</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">数量</th>
                    <th class="px-3 py-1.5 text-right w-[90px]">单价</th>
                    <th class="px-3 py-1.5 text-right w-[90px]">小计</th>
                    <th class="px-3 py-1.5 text-right w-[90px]">促销价</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">实际数量</th>
                    <th class="px-3 py-1.5 text-right w-[90px]">实际小计</th>
                    <th class="px-3 py-1.5 w-[60px]"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="item-{{ $item->id }}">
                    <td class="px-3 py-1.5 truncate">
                        <span class="text-foreground">{{ $item->product_name }}</span>
                    </td>
                    <td class="px-3 py-1.5 text-muted-foreground truncate">
                        @if(is_array($item->sku_specs)){{ implode(', ', $item->sku_specs) }}@else{{ $item->sku_specs ?? '-' }}@endif
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->quantity }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ money_format($item->price) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums font-medium">{{ money_format($item->subtotal) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums {{ $item->strategy_price ? 'text-blue-600 font-medium' : 'text-muted-foreground' }}">
                        {{ $item->strategy_price ? money_format($item->strategy_price) : '-' }}
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums {{ $item->actual_quantity && $item->actual_quantity != $item->quantity ? 'text-orange-600 font-medium' : '' }}">
                        {{ $item->actual_quantity ?: '-' }}
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">
                        {{ $item->actual_subtotal ? money_format($item->actual_subtotal) : '-' }}
                    </td>
                    <td class="px-3 py-1.5">
                        <div class="flex items-center gap-1">
                            @if($canEditItems)
                                <button type="button" wire:click="openEditItemModal({{ $item->id }})" class="text-blue-500 hover:text-blue-700" title="编辑">
                                    <x-ui.icon name="pencil" class="w-3.5 h-3.5" />
                                </button>
                                <button type="button" wire:click="confirmDeleteItem({{ $item->id }})" class="text-red-500 hover:text-red-700" title="删除">
                                    <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-3 py-6 text-center text-muted-foreground">暂无明细</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- 合计行 --}}
        @if($order->items->isNotEmpty())
        <div class="flex items-center justify-end gap-6 px-4 py-2 border-t bg-muted/20 text-sm">
            <span class="text-muted-foreground">合计数量：<b class="text-foreground">{{ $order->items->sum('quantity') }}</b></span>
            <span class="text-muted-foreground">合计金额：<b class="text-foreground">{{ money_format($order->total_amount) }}</b></span>
        </div>
        @endif
    </div>

    {{-- 添加/编辑明细弹窗 --}}
    @if($showAddItemModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingItemId ? '编辑订单明细' : '添加订单明细' }}</h2>
                <div class="flex items-center gap-2">
                    @if($editingItemId)
                        <button type="button" wire:click="confirmDeleteItem({{ $editingItemId }})" class="text-red-500 hover:text-red-700 text-xs inline-flex items-center gap-1 border border-red-300 rounded px-2 py-1 hover:bg-red-50 transition-colors">
                            <x-ui.icon name="trash" class="w-3 h-3" /> 删除
                        </button>
                    @endif
                    <button type="button" wire:click="cancelAddItem" class="text-muted-foreground hover:text-foreground transition-colors">
                        <x-ui.icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>
            </div>
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">数量 <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formQuantity" min="1" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('formQuantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">单价（元） <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="number" wire:model="formPrice" min="0" step="0.01" placeholder="0.00" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            <button type="button" wire:click="autoPrice" class="shrink-0 rounded-md border border-blue-300 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 transition-colors whitespace-nowrap">取价</button>
                        </div>
                        @error('formPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="cancelAddItem" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="saveItem" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingItemId ? '保存' : '添加' }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 保存确认弹窗 --}}
    @if($showSaveConfirm)
    <div class="fixed inset-0 z-[60] flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">{{ $editingItemId ? '确认保存修改？' : '确认添加明细？' }}</h2>
            <div class="rounded-md bg-muted p-3 mb-6 text-sm text-foreground whitespace-pre-line">{{ $saveConfirmTitle }}</div>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeSaveConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">返回修改</button>
                <button type="button" wire:click="executeSaveItem" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 取消编辑确认弹窗 --}}
    @if($showCancelConfirm)
    <div class="fixed inset-0 z-[60] flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">放弃编辑？</h2>
            <p class="text-sm text-muted-foreground mb-6">您已填写部分数据，确认放弃编辑？未保存的内容将丢失。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeCancelConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">继续编辑</button>
                <button type="button" wire:click="confirmCancelEdit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">放弃</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除明细确认弹窗 --}}
    @if($showDeleteItemConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确认删除此订单明细？此操作不可撤销。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteItemConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="deleteItem" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 状态确认弹窗 --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认操作</h2>
            <p class="text-sm text-muted-foreground mb-6">{{ $confirmTitle }}</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeConfirmModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeConfirm" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 导出弹窗 --}}
    @if($showExportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">导出订单明细</h2>
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-sku" checked disabled class="rounded border-input" />
                    <label for="exp-sku" class="text-sm">SKU编码 / 商品名称 / 规格</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-qty" checked disabled class="rounded border-input" />
                    <label for="exp-qty" class="text-sm">数量 / 单价 / 小计</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-strategy" wire:model="exportStrategy" class="rounded border-input" />
                    <label for="exp-strategy" class="text-sm">促销价</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="exp-actual" wire:model="exportActual" class="rounded border-input" />
                    <label for="exp-actual" class="text-sm">实际数量 / 实际小计</label>
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
            <h2 class="text-lg font-semibold text-foreground mb-4">导入订单明细</h2>
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
                    <p>SKU编码 | 数量 | 单价（元）</p>
                    <p class="mt-1 text-[11px]">注：已存在的 SKU 将被跳过，不会重复添加</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeImportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeImport" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">导入</button>
            </div>
        </div>
    </div>
    @endif
</div>
