<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('purchase-returns') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $return->return_no }}</h1>
            {!! status_badge($return->status, 'purchase_return') !!}
        </div>
        <div class="flex items-center gap-2">
            @if($return->status === 1)
                <button type="button" wire:click="confirmApprove" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">审核</button>
            @elseif($return->status === 2)
                <button type="button" wire:click="confirmShip" class="rounded-md bg-orange-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-700 transition-colors">出库</button>
            @elseif($return->status === 3)
                <button type="button" wire:click="confirmComplete" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">完成</button>
            @endif

            @if(in_array($return->status, [1, 2]))
                <button type="button" wire:click="confirmCancel" class="rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">取消</button>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-4">
        <span>采购单：<b class="text-foreground"><a href="{{ route('purchase-orders.detail', $return->purchase_order_id) }}" class="text-blue-600 hover:text-blue-700">{{ $return->purchaseOrder?->order_no ?? '-' }}</a></b></span>
        <span>供应商：<b class="text-foreground">{{ $return->supplier?->name ?? '-' }}</b></span>
        <span>仓库：<b class="text-foreground">{{ $return->warehouse?->name ?? '-' }}</b></span>
        @if($return->operator)<span>经办人：<b class="text-foreground">{{ $return->operator->name }}</b></span>@endif
        <span>退货金额：<b class="text-foreground">{{ money_format($return->total_amount) }}</b></span>
        <span>实际金额：<b class="text-foreground">{{ money_format($return->actual_amount) }}</b></span>
        @if($return->reason)<span>原因：<b class="text-foreground">{{ $return->reason }}</b></span>@endif
        @if($return->remark)<span>备注：<b class="text-foreground">{{ $return->remark }}</b></span>@endif
    </div>

    {{-- 明细表格 --}}
    <div class="rounded-lg border bg-card">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">退货明细</h2>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                    <th class="px-3 py-1.5 text-left">SKU</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">退货数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退货单价</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退货金额</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">实际数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">实际金额</th>
                    <th class="px-3 py-1.5 text-left w-[120px]">原因</th>
                </tr>
            </thead>
            <tbody>
                @forelse($return->items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="ri-{{ $item->id }}">
                    <td class="px-3 py-1.5 truncate">
                        <span class="font-mono text-foreground">{{ $item->sku?->sku_code }}</span>
                        <span class="text-muted-foreground ml-1">{{ $item->sku?->product?->name }}</span>
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->quantity }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ money_format($item->price) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums font-medium">{{ money_format($item->amount) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->actual_quantity ?: '-' }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->actual_amount ? money_format($item->actual_amount) : '-' }}</td>
                    <td class="px-3 py-1.5 text-muted-foreground truncate">{{ $item->reason ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-3 py-6 text-center text-muted-foreground">暂无明细</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 时间线 --}}
    <div class="mt-4 text-sm text-muted-foreground">
        <div class="flex flex-wrap gap-x-6 gap-y-1">
            <span>创建时间：{{ $return->created_at?->format('Y-m-d H:i') ?? '-' }}</span>
            @if($return->audited_at)<span>审核时间：{{ $return->audited_at->format('Y-m-d H:i') }}</span>@endif
            @if($return->shipped_at)<span>出库时间：{{ $return->shipped_at->format('Y-m-d H:i') }}</span>@endif
            @if($return->completed_at)<span>完成时间：{{ $return->completed_at->format('Y-m-d H:i') }}</span>@endif
            @if($return->cancelled_at)<span>取消时间：{{ $return->cancelled_at->format('Y-m-d H:i') }}</span>@endif
        </div>
    </div>

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
</div>
