<div class="" x-data="{ activeTab: 'info' }">
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

    {{-- Tab 切换 --}}
    <div class="border-b mb-4">
        <nav class="flex gap-6">
            <button type="button" @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                发票信息
            </button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                状态变更记录
                @if($auditLogs->count() > 0)
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-muted text-muted-foreground text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $auditLogs->count() }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab 内容：发票信息 --}}
    <div x-show="activeTab === 'info'">
        <div class="rounded-lg border bg-card">
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
    </div>

    {{-- Tab 内容：状态变更记录 --}}
    <div x-show="activeTab === 'logs'" x-transition>
        @if($auditLogs->isNotEmpty())
        <div class="rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                        <th class="px-4 py-2 text-left w-[180px]">时间</th>
                        <th class="px-4 py-2 text-left w-[120px]">操作</th>
                        <th class="px-4 py-2 text-left">状态变更</th>
                        <th class="px-4 py-2 text-left w-[120px]">操作人</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditLogs as $log)
                    <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2 text-muted-foreground tabular-nums">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium
                                @if($log->action === 'cancel') bg-red-100 text-red-700
                                @elseif($log->action === 'rollback') bg-purple-100 text-purple-700
                                @elseif($log->action === 'status_change') bg-blue-100 text-blue-700
                                @elseif($log->action === 'create') bg-green-100 text-green-700
                                @elseif($log->action === 'approve') bg-green-100 text-green-700
                                @elseif($log->action === 'complete') bg-green-100 text-green-700
                                @else bg-muted text-muted-foreground
                                @endif
                            ">{{ $log->action_label }}</span>
                        </td>
                        <td class="px-4 py-2">
                            @php
                                $before = $log->before_data ?? [];
                                $after = $log->after_data ?? [];
                                $beforeStatus = $before['status'] ?? null;
                                $afterStatus = $after['status'] ?? null;
                                $invoiceStatusMap = [
                                    \App\Models\Invoice::STATUS_PENDING => ['label' => '待开具', 'color' => 'yellow'],
                                    \App\Models\Invoice::STATUS_ISSUED => ['label' => '已开具', 'color' => 'green'],
                                    \App\Models\Invoice::STATUS_SENT => ['label' => '已寄出', 'color' => 'blue'],
                                ];
                            @endphp
                            @if($beforeStatus && $afterStatus && isset($invoiceStatusMap[$beforeStatus]) && isset($invoiceStatusMap[$afterStatus]))
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $invoiceStatusMap[$beforeStatus]['color'] }}-100 text-{{ $invoiceStatusMap[$beforeStatus]['color'] }}-700">{{ $invoiceStatusMap[$beforeStatus]['label'] }}</span>
                                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5 inline text-muted-foreground mx-1" />
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $invoiceStatusMap[$afterStatus]['color'] }}-100 text-{{ $invoiceStatusMap[$afterStatus]['color'] }}-700">{{ $invoiceStatusMap[$afterStatus]['label'] }}</span>
                            @else
                                <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-foreground">{{ $log->operator?->name ?? '系统' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="rounded-lg border bg-card px-6 py-12 text-center text-sm text-muted-foreground">暂无状态变更记录</div>
        @endif
    </div>
</div>
