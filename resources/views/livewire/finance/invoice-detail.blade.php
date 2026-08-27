<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $invoice->invoice_no }}</h1>
            @php $sc = $statusColorMap[$invoice->status] ?? 'gray'; @endphp
            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ $statusMap[$invoice->status] ?? '-' }}</span>
        </div>
        <div class="flex items-center gap-2">
            @can('finance.invoice.create')
            @if($invoice->status === \App\Models\Invoice::STATUS_PENDING)
                <button type="button" wire:click="issue" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">开具</button>
            @endif
            @if($invoice->status === \App\Models\Invoice::STATUS_ISSUED)
                <button type="button" wire:click="send" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">寄出</button>
            @endif
            @endcan
            @can('finance.invoice.edit')
            @if($invoice->canEdit())
                <a href="{{ route('invoices') }}?edit={{ $invoice->id }}" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">编辑</a>
            @endif
            @endcan
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>类型：<b class="text-foreground">{{ $typeMap[$invoice->type] ?? '-' }}</b></span>
        <span>{{ $invoice->type === \App\Models\Invoice::TYPE_CUSTOMER ? '商家' : '供应商' }}：<b class="text-foreground">{{ $invoice->merchant?->name ?? $invoice->supplier?->name ?? '-' }}</b></span>
        @if($invoice->applied_at)
            <span>申请时间：<b class="text-foreground">{{ $invoice->applied_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($invoice->issued_at)
            <span>开具时间：<b class="text-foreground">{{ $invoice->issued_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($invoice->sent_at)
            <span>寄出时间：<b class="text-foreground">{{ $invoice->sent_at->format('Y-m-d H:i') }}</b></span>
        @endif
    </div>

    {{-- 发票信息 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">发票信息</h2>
        </div>
        <div class="px-4 py-3 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">发票金额</span>
                <span class="text-foreground font-bold text-base">{{ money_format($invoice->amount) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">发票抬头</span>
                <span class="text-foreground">{{ $invoice->title ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">税号</span>
                <span class="text-foreground font-mono">{{ $invoice->tax_no ?? '-' }}</span>
            </div>
            @if($invoice->file_url)
            <div class="flex justify-between">
                <span class="text-muted-foreground">发票文件</span>
                <a href="{{ $invoice->file_url }}" target="_blank" class="text-blue-600 hover:underline">查看文件</a>
            </div>
            @endif
        </div>
    </div>

    {{-- 状态轨迹 --}}
    <div class="rounded-lg border bg-card">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">状态轨迹</h2>
        </div>
        <div class="divide-y">
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full @if($invoice->status >= 1) bg-yellow-500 @else bg-gray-300 @endif"></span>
                    <div>
                        <div class="text-sm text-foreground font-medium">待开具</div>
                        @if($invoice->applied_at)
                        <div class="text-xs text-muted-foreground mt-0.5">{{ $invoice->applied_at->format('Y-m-d H:i') }}</div>
                        @endif
                    </div>
                </div>
                @if($invoice->status >= 1)
                    <span class="text-green-600 text-xs">已完成</span>
                @endif
            </div>
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full @if($invoice->status >= 2) bg-green-500 @else bg-gray-300 @endif"></span>
                    <div>
                        <div class="text-sm text-foreground font-medium">已开具</div>
                        @if($invoice->issued_at)
                        <div class="text-xs text-muted-foreground mt-0.5">{{ $invoice->issued_at->format('Y-m-d H:i') }}</div>
                        @endif
                    </div>
                </div>
                @if($invoice->status >= 2)
                    <span class="text-green-600 text-xs">已完成</span>
                @elseif($invoice->status === 1)
                    <span class="text-yellow-600 text-xs">进行中</span>
                @endif
            </div>
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full @if($invoice->status >= 3) bg-blue-500 @else bg-gray-300 @endif"></span>
                    <div>
                        <div class="text-sm text-foreground font-medium">已寄出</div>
                        @if($invoice->sent_at)
                        <div class="text-xs text-muted-foreground mt-0.5">{{ $invoice->sent_at->format('Y-m-d H:i') }}</div>
                        @endif
                    </div>
                </div>
                @if($invoice->status >= 3)
                    <span class="text-green-600 text-xs">已完成</span>
                @elseif($invoice->status === 2)
                    <span class="text-yellow-600 text-xs">进行中</span>
                @endif
            </div>
        </div>
    </div>
</div>
