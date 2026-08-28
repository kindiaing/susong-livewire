<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('supplier-settlements') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $settlement->settlement_no }}</h1>
            {!! status_badge($settlement->status, 'supplier_settlement') !!}
        </div>
        <div class="flex items-center gap-2">
            @can('finance.supplier-settlement.create')
            @if(in_array($settlement->status, [\App\Models\SupplierSettlement::STATUS_PENDING, \App\Models\SupplierSettlement::STATUS_PARTIAL]))
                <button type="button" wire:click="openPaymentModal" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">登记付款</button>
            @endif
            @endcan
            @if($settlement->status !== \App\Models\SupplierSettlement::STATUS_CLOSED)
                <button type="button" wire:click="openCloseModal" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">办结</button>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>供应商：<b class="text-foreground">{{ $settlement->supplier?->name ?? '-' }}</b></span>
        <span>结算周期：<b class="text-foreground">{{ $settlement->start_date?->format('Y-m-d') ?? '-' }} ~ {{ $settlement->end_date?->format('Y-m-d') ?? '-' }}</b></span>
        @if($settlement->settled_at)
            <span>结清时间：<b class="text-foreground">{{ $settlement->settled_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($settlement->closed_at)
            <span>办结时间：<b class="text-foreground">{{ $settlement->closed_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($settlement->closed_by)
            <span>办结人：<b class="text-foreground">{{ $settlement->closedBy?->name ?? '-' }}</b></span>
        @endif
    </div>

    {{-- 金额构成明细 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">金额构成明细</h2>
        </div>
        <div class="px-4 py-3 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">汇总金额（实际入库）</span>
                <span class="text-foreground font-medium">{{ money_format($settlement->total_amount) }}</span>
            </div>
            @if($settlement->return_amount > 0)
            <div class="flex justify-between">
                <span class="text-muted-foreground">− 退货扣减</span>
                <span class="text-orange-600 font-medium">− {{ money_format($settlement->return_amount) }}</span>
            </div>
            @endif
            @if($settlement->service_fee > 0)
            <div class="flex justify-between">
                <span class="text-muted-foreground">− 服务费</span>
                <span class="text-orange-600 font-medium">− {{ money_format($settlement->service_fee) }}</span>
            </div>
            @endif
            <div class="flex justify-between border-t pt-2">
                <span class="text-foreground font-medium">应付金额</span>
                <span class="text-foreground font-bold text-base">{{ money_format($settlement->payable_amount) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">已付金额</span>
                <span class="text-green-600 font-medium">{{ money_format($settlement->paid_amount) }}</span>
            </div>
            <div class="flex justify-between border-t pt-2">
                <span class="text-foreground font-medium">待付金额</span>
                <span class="@if($remainingAmount > 0) text-red-600 @else text-green-600 @endif font-bold text-base">{{ money_format($remainingAmount) }}</span>
            </div>
        </div>
    </div>

    {{-- 结算明细（关联采购单） --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">结算明细（{{ $settlement->items->count() }} 张采购单，按实际入库金额计价）</h2>
        </div>
        @if($settlement->items->count())
        <div class="divide-y">
            @foreach($settlement->items as $si)
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <div>
                        <div class="text-sm text-foreground font-mono">{{ $si->purchaseOrder?->order_no ?? "PO-{$si->purchase_order_id}" }}</div>
                        @if($si->purchaseOrder && $si->purchaseOrder->actual_amount != $si->purchaseOrder->total_amount)
                        <div class="text-xs text-orange-600 mt-0.5" title="入库差异已按实收计价">订购 {{ money_format($si->purchaseOrder->total_amount) }} → 实际 {{ money_format($si->purchaseOrder->actual_amount) }}</div>
                        @endif
                    </div>
                </div>
                <span class="text-sm text-foreground font-medium">{{ money_format($si->amount) }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-4 py-8 text-center text-sm text-muted-foreground">暂无结算明细</div>
        @endif
    </div>

    {{-- 付款记录时间线 --}}
    <div class="rounded-lg border bg-card">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">付款记录（{{ $settlement->payments->count() }}）</h2>
        </div>
        @if($settlement->payments->count())
        <div class="divide-y">
            @foreach($settlement->payments as $payment)
            <div class="px-4 py-3 flex items-center justify-between {{ $payment->approval_status === \App\Models\SettlementPayment::APPROVAL_REJECTED ? 'opacity-50' : '' }}">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full {{ $payment->approval_status === \App\Models\SettlementPayment::APPROVAL_REJECTED ? 'bg-gray-400' : 'bg-green-500' }}"></span>
                    <div>
                        <div class="text-sm text-foreground font-medium">{{ money_format($payment->amount) }}</div>
                        <div class="text-xs text-muted-foreground mt-0.5">
                            {{ $paymentMethodMap[$payment->payment_method] ?? '-' }}
                            · {{ $payment->created_at?->format('Y-m-d H:i') }}
                        </div>
                        @if($payment->remark)
                        <div class="text-xs text-muted-foreground mt-0.5">备注：{{ $payment->remark }}</div>
                        @endif
                    </div>
                </div>
                @php
                    $approvalColors = [
                        \App\Models\SettlementPayment::APPROVAL_PENDING => 'bg-orange-100 text-orange-700',
                        \App\Models\SettlementPayment::APPROVAL_APPROVED => 'bg-green-100 text-green-700',
                        \App\Models\SettlementPayment::APPROVAL_REJECTED => 'bg-gray-100 text-gray-600',
                    ];
                    $approvalColor = $approvalColors[$payment->approval_status] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $approvalColor }}">
                    {{ $payment->approval_status_label }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-4 py-8 text-center text-sm text-muted-foreground">暂无付款记录</div>
        @endif
    </div>

    {{-- 付款弹窗 --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认付款</h2>
                <button type="button" wire:click="closePaymentModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-3 mb-4">
                <div class="text-sm text-muted-foreground">
                    剩余应付：<span class="text-foreground font-medium">{{ money_format($remainingAmount) }} 元</span>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">本次付款金额（元） <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="paymentAmount" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入付款金额" />
                    @error('paymentAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="paymentRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closePaymentModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="submitPayment" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认付款</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 办结弹窗 --}}
    @if($showCloseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认办结</h2>
                <button type="button" wire:click="closeCloseModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="text-sm text-muted-foreground">
                    办结后此结算单将不可再操作付款，确认要办结吗？
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
