<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('order-returns') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $return->return_no }}</h1>
            {!! status_badge($return->status, 'order_return') !!}
        </div>
        <div class="flex items-center gap-2">
            @if($return->status === 1)
                <button type="button" wire:click="confirmApprove" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">审核</button>
            @elseif($return->status === 2)
                <button type="button" wire:click="confirmReturn" class="rounded-md bg-orange-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-700 transition-colors">确认退货</button>
            @elseif($return->status === 3)
                <button type="button" wire:click="confirmRefund" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">完成退款</button>
            @endif

            @if(in_array($return->status, [1, 2]))
                <button type="button" wire:click="confirmCancel" class="rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">作废</button>
            @endif

            {{-- 超管状态回退 --}}
            @if($isSuperAdmin && !in_array($return->status, [1, 9]))
                <div class="relative" x-data="{open: false}">
                    <button type="button" @click="open = !open" class="rounded-md border border-purple-300 px-3 py-1.5 text-sm font-medium text-purple-600 hover:bg-purple-50 transition-colors">回退</button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-36 rounded-md border bg-background shadow-lg z-10 py-1">
                        @if($return->status > 1)<button type="button" wire:click="confirmRollback(1)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到待审核</button>@endif
                        @if($return->status > 2)<button type="button" wire:click="confirmRollback(2)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到已审核</button>@endif
                        @if($return->status > 3)<button type="button" wire:click="confirmRollback(3)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到已退货</button>@endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-4">
        <span>订单：<b class="text-foreground"><a href="{{ route('orders.detail', $return->order_id) }}" class="text-blue-600 hover:text-blue-700">{{ $return->order?->order_no ?? '-' }}</a></b></span>
        <span>商家：<b class="text-foreground">{{ $return->merchant?->name ?? '-' }}</b></span>
        @if($return->operator)<span>经办人：<b class="text-foreground">{{ $return->operator->name }}</b></span>@endif
        <span>退货金额：<b class="text-foreground">{{ money_format($return->total_amount) }}</b></span>
        <span>退款金额：<b class="text-foreground">{{ money_format($return->refund_amount) }}</b></span>
        @if($return->reason)<span>原因：<b class="text-foreground">{{ $return->reason }}</b></span>@endif
        @if($return->remark)<span>备注：<b class="text-foreground">{{ $return->remark }}</b></span>@endif
    </div>

    {{-- 明细表格 --}}
    <div class="rounded-lg border bg-card">
        <div class="px-4 py-2 border-b flex items-center justify-between">
            <h2 class="text-sm font-semibold text-foreground">退货明细</h2>
            @if($return->status === 1)
            <div class="flex items-center gap-2">
                <button type="button" wire:click="addItemRow" class="rounded-md border border-input px-2 py-1 text-xs hover:bg-accent transition-colors">+ 添加行</button>
                @if(count($formItems) > 0)
                <button type="button" wire:click="exportItems" class="rounded-md border border-input px-2 py-1 text-xs hover:bg-accent transition-colors">导出明细</button>
                @endif
                <button type="button" wire:click="saveItems" class="rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">保存明细</button>
            </div>
            @endif
        </div>

        @if($return->status === 1)
        {{-- 待审核：可编辑明细 --}}
        @if(count($formItems) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[720px]">
                <thead>
                    <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                        <th class="px-3 py-1.5 text-left">订单明细</th>
                        <th class="px-3 py-1.5 text-right w-[80px]">可退数量</th>
                        <th class="px-3 py-1.5 text-right w-[110px]">退货数量</th>
                        <th class="px-3 py-1.5 text-right w-[90px]">单价（元）</th>
                        <th class="px-3 py-1.5 text-left w-[160px]">原因</th>
                        <th class="px-3 py-1.5 text-center w-[50px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($formItems as $i => $row)
                    <tr wire:key="order-return-item-{{ $i }}">
                        <td class="px-3 py-1.5">
                            <select wire:change="pickOrderItem({{ $i }}, $event.target.value)" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs">
                                <option value="0">选择订单明细</option>
                                @foreach($orderItems as $oi)
                                <option value="{{ $oi->id }}" @selected((int) ($row['order_item_id'] ?? 0) === $oi->id)>
                                    {{ $oi->sku?->product?->name ?? $oi->product_name ?? 'SKU' }}（{{ $oi->sku?->sku_code }}）实收 {{ $oi->actual_quantity ?: $oi->quantity }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-1.5 text-right text-muted-foreground tabular-nums">{{ $row['max_quantity'] ?: '-' }}</td>
                        <td class="px-3 py-1.5">
                            <input type="number" wire:model="formItems.{{ $i }}.quantity" min="0" max="{{ $row['max_quantity'] }}" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs text-right" />
                        </td>
                        <td class="px-3 py-1.5 text-right text-muted-foreground tabular-nums">{{ number_format(($row['price'] ?? 0) / 1000, 2) }}</td>
                        <td class="px-3 py-1.5">
                            <input type="text" wire:model="formItems.{{ $i }}.reason" placeholder="可选" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs" />
                        </td>
                        <td class="px-3 py-1.5 text-center">
                            <button type="button" wire:click="removeItemRow({{ $i }})" class="text-red-500 hover:text-red-700" title="删除行"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="px-4 py-2 text-xs text-muted-foreground border-t">退货数量不能超过客户实收数量；数量填 0 或删除行表示该明细不退货。编辑后请点击「保存明细」生效。</p>
        @else
        <div class="px-4 py-8 text-center text-sm text-muted-foreground">
            暂无明细。点击右上角「+ 添加行」录入退货商品，保存明细后方可审核。
        </div>
        @endif
        @else
        {{-- 非待审核：只读明细 --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                    <th class="px-3 py-1.5 text-left">SKU</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">退货数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退货单价</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退货金额</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退款金额</th>
                    <th class="px-3 py-1.5 text-left w-[120px]">原因</th>
                </tr>
            </thead>
            <tbody>
                @forelse($return->items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="ori-{{ $item->id }}">
                    <td class="px-3 py-1.5 truncate">
                        <span class="font-mono text-foreground">{{ $item->sku?->sku_code }}</span>
                        <span class="text-muted-foreground ml-1">{{ $item->sku?->product?->name }}</span>
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{!! format_quantity($item->sku_id, $item->quantity, $item->sku?->baseUnit?->name) !!}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ money_format($item->price) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums font-medium">{{ money_format($item->amount) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->refund_amount ? money_format($item->refund_amount) : '-' }}</td>
                    <td class="px-3 py-1.5 text-muted-foreground truncate">{{ $item->reason ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-3 py-6 text-center text-muted-foreground">暂无明细</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>

    {{-- 时间线 --}}
    <div class="mt-4 text-sm text-muted-foreground">
        <div class="flex flex-wrap gap-x-6 gap-y-1">
            <span>创建时间：{{ $return->created_at?->format('Y-m-d H:i') ?? '-' }}</span>
            @if($return->audited_at)<span>审核时间：{{ $return->audited_at->format('Y-m-d H:i') }}</span>@endif
            @if($return->returned_at)<span>退货时间：{{ $return->returned_at->format('Y-m-d H:i') }}</span>@endif
            @if($return->refunded_at)<span>退款时间：{{ $return->refunded_at->format('Y-m-d H:i') }}</span>@endif
            @if($return->cancelled_at)<span>作废时间：{{ $return->cancelled_at->format('Y-m-d H:i') }}</span>@endif
        </div>
    </div>

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
