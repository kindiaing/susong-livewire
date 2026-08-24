<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('receivables') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $receivable->receivable_no }}</h1>
            {!! status_badge($receivable->status, 'receivable') !!}
            @if($isOverdue)
                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-red-100 text-red-700" title="到期日 {{ $receivable->due_date->format('Y-m-d') }}">超期</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if(!in_array($receivable->status, [\App\Models\Receivable::STATUS_SETTLED, \App\Models\Receivable::STATUS_CLOSED]))
                <button type="button" wire:click="openReceiveModal" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">登记收款</button>
            @endif
            @if($receivable->status !== \App\Models\Receivable::STATUS_CLOSED)
                <button type="button" wire:click="openCloseModal" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">办结</button>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>订单：<b class="text-foreground"><a href="{{ route('orders.detail', $receivable->order_id) }}" class="text-blue-600 hover:text-blue-700">{{ $receivable->order?->order_no ?? '-' }}</a></b></span>
        <span>商家：<b class="text-foreground">{{ $receivable->merchant?->name ?? '-' }}</b></span>
        <span>结算方式：<b class="text-foreground">{{ $settlementMap[$receivable->settlement_type] ?? '-' }}</b></span>
        @if($receivable->due_date)
            <span>到期日：<b class="text-foreground @if($isOverdue) text-red-600 @endif">{{ $receivable->due_date->format('Y-m-d') }}</b></span>
        @endif
        @if($receivable->settled_at)
            <span>结清时间：<b class="text-foreground">{{ $receivable->settled_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($receivable->closed_at)
            <span>办结时间：<b class="text-foreground">{{ $receivable->closed_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($receivable->remark)
            <span>备注：<b class="text-foreground">{{ $receivable->remark }}</b></span>
        @endif
    </div>

    {{-- 金额构成明细 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">金额构成明细</h2>
        </div>
        <div class="px-4 py-3 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">原始应收金额</span>
                <span class="text-foreground font-medium">{{ money_format($receivable->original_amount) }}</span>
            </div>
            @if($receivable->discrepancy_amount > 0)
            <div class="flex justify-between">
                <span class="text-muted-foreground">− 差异扣减</span>
                <span class="text-orange-600 font-medium">− {{ money_format($receivable->discrepancy_amount) }}</span>
            </div>
            @endif
            @if($receivable->return_amount > 0)
            <div class="flex justify-between">
                <span class="text-muted-foreground">− 售后退货扣减</span>
                <span class="text-orange-600 font-medium">− {{ money_format($receivable->return_amount) }}</span>
            </div>
            @endif
            @if($receivable->strategy_discount_amount > 0)
            <div class="flex justify-between">
                <span class="text-muted-foreground">− 改价/促销折扣</span>
                <span class="text-orange-600 font-medium">− {{ money_format($receivable->strategy_discount_amount) }}</span>
            </div>
            @endif
            <div class="flex justify-between border-t pt-2">
                <span class="text-foreground font-medium">调整后应收金额</span>
                <span class="text-foreground font-bold text-base">{{ money_format($receivable->adjusted_amount) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">已收金额</span>
                <span class="text-green-600 font-medium">{{ money_format($receivable->received_amount) }}</span>
            </div>
            <div class="flex justify-between border-t pt-2">
                <span class="text-foreground font-medium">待收金额</span>
                <span class="@if($remainingAmount > 0) text-red-600 @else text-green-600 @endif font-bold text-base">{{ money_format($remainingAmount) }}</span>
            </div>
        </div>
    </div>

    {{-- 收款记录时间线 --}}
    <div class="rounded-lg border bg-card">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">收款记录 ({{ $receivable->payments->count() }})</h2>
        </div>
        @if($receivable->payments->count())
        <div class="divide-y">
            @foreach($receivable->payments as $payment)
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <div>
                        <div class="text-sm text-foreground font-medium">{{ money_format($payment->amount) }}</div>
                        <div class="text-xs text-muted-foreground mt-0.5">
                            {{ $paymentMethodMap[$payment->payment_method] ?? '-' }}
                            @if($payment->operator) · {{ $payment->operator->name }}@endif
                            · {{ $payment->created_at?->format('Y-m-d H:i') }}
                        </div>
                        @if($payment->remark)
                        <div class="text-xs text-muted-foreground mt-0.5">备注：{{ $payment->remark }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-4 py-8 text-center text-sm text-muted-foreground">暂无收款记录</div>
        @endif
    </div>

    {{-- 收款弹窗 --}}
    @if($showReceiveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true" wire:click="closeReceiveModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">登记收款</h2>
                <button type="button" wire:click="closeReceiveModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-3 mb-4">
                <div class="text-sm text-muted-foreground">
                    应收余额：<span class="text-foreground font-medium">{{ money_format($remainingAmount) }} 元</span>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">本次收款金额（元） <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="receiveAmount" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入收款金额" />
                    @error('receiveAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="receiveRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeReceiveModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="submitReceive" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认收款</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 办结弹窗 --}}
    @if($showCloseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true" wire:click="closeCloseModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认办结</h2>
                <button type="button" wire:click="closeCloseModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="text-sm text-muted-foreground">
                    办结后此应收单将不可再操作收款，确认要办结吗？
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="closeRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeCloseModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="submitClose" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认办结</button>
            </div>
        </div>
    </div>
    @endif
</div>
