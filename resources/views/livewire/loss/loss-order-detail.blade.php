<div class="" x-data="{ activeTab: 'items' }">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('loss-orders') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $lossOrder->loss_no }}</h1>
            {!! status_badge($lossOrder->status, 'loss_status') !!}
            {!! status_badge($lossOrder->approval_status, 'loss_approval') !!}
        </div>
        <div class="flex items-center gap-2">
            @can('loss.loss-order.edit')
            @if($lossOrder->approval_status === \App\Models\LossOrder::APPROVAL_PENDING)
                <button type="button" wire:click="confirmApprove" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">审核</button>
            @endif
            @if($lossOrder->status === \App\Models\LossOrder::STATUS_APPROVED && $lossOrder->source_type !== 'purchase_order')
                <button type="button" wire:click="execute" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">执行</button>
            @endif
            @if($lossOrder->status === \App\Models\LossOrder::STATUS_EXECUTED || ($lossOrder->status === \App\Models\LossOrder::STATUS_APPROVED && $lossOrder->source_type === 'purchase_order'))
                <button type="button" wire:click="close" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors" title="{{ $lossOrder->source_type === 'purchase_order' && $lossOrder->status === 2 ? '采购入库差异仅账务留痕不扣库存，审核后可直接关闭' : '' }}">关闭</button>
            @endif
            @if($lossOrder->canEdit())
                <a href="{{ route('loss-orders') }}?edit={{ $lossOrder->id }}" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">编辑</a>
            @endif
            @endcan
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>仓库：<b class="text-foreground">{{ $lossOrder->warehouse?->name ?? '-' }}</b></span>
        <span>损耗类型：<b class="text-foreground">{{ $typeMap[$lossOrder->loss_type] ?? '-' }}</b></span>
        <span>来源：<b class="text-foreground">{{ $lossOrder->source_type_label }}</b></span>
        @if($lossOrder->applicant)
            <span>申请人：<b class="text-foreground">{{ $lossOrder->applicant->name }}</b></span>
        @endif
        @if($lossOrder->reviewer)
            <span>审核人：<b class="text-foreground">{{ $lossOrder->reviewer->name }}</b></span>
        @endif
        @if($lossOrder->reviewed_at)
            <span>审核时间：<b class="text-foreground">{{ $lossOrder->reviewed_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($lossOrder->executed_at)
            <span>执行时间：<b class="text-foreground">{{ $lossOrder->executed_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($lossOrder->closed_at)
            <span>关闭时间：<b class="text-foreground">{{ $lossOrder->closed_at->format('Y-m-d H:i') }}</b></span>
        @endif
    </div>

    {{-- Tab 切换 --}}
    <div class="border-b mb-4">
        <nav class="flex gap-6">
            <button type="button" @click="activeTab = 'items'" :class="activeTab === 'items' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">损耗明细</button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                状态变更记录
                @if($lossOrder->auditLogs->isNotEmpty())
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-muted text-muted-foreground text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $lossOrder->auditLogs->count() }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab 内容：损耗明细 --}}
    <div x-show="activeTab === 'items'" x-transition>
    {{-- 金额信息 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">损耗金额</h2>
        </div>
        <div class="px-4 py-3 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">损耗总金额</span>
                <span class="text-foreground font-bold text-base">{{ money_format($lossOrder->total_amount) }}</span>
            </div>
            @if($lossOrder->reason)
            <div class="flex justify-between">
                <span class="text-muted-foreground">损耗原因</span>
                <span class="text-foreground text-right max-w-[400px]">{{ $lossOrder->reason }}</span>
            </div>
            @endif
            @if($lossOrder->remark)
            <div class="flex justify-between">
                <span class="text-muted-foreground">备注</span>
                <span class="text-foreground text-right max-w-[400px]">{{ $lossOrder->remark }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- 损耗明细 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">损耗明细（{{ $lossOrder->items->count() }} 项）</h2>
        </div>
        @if($lossOrder->items->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        <th class="px-4 py-2 text-left">SKU</th>
                        <th class="px-4 py-2 text-left">损耗类型</th>
                        <th class="px-4 py-2 text-right">数量</th>
                        <th class="px-4 py-2 text-right">成本价</th>
                        <th class="px-4 py-2 text-right">损耗金额</th>
                        <th class="px-4 py-2 text-left">责任方</th>
                        <th class="px-4 py-2 text-left">原因</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($lossOrder->items as $item)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2 text-foreground">{{ $item->sku?->name ?? "SKU-{$item->sku_id}" }}</td>
                        <td class="px-4 py-2 text-foreground">{{ $typeMap[$item->loss_type] ?? '-' }}</td>
                        <td class="px-4 py-2 text-right text-foreground">{!! format_quantity($item->sku_id, $item->quantity, $item->sku?->baseUnit?->name) !!}</td>
                        <td class="px-4 py-2 text-right text-foreground">{{ money_format($item->cost_price) }}</td>
                        <td class="px-4 py-2 text-right text-foreground font-medium">{{ money_format($item->loss_amount) }}</td>
                        <td class="px-4 py-2 text-foreground">{{ $responsiblePartyMap[$item->responsible_party] ?? '-' }}</td>
                        <td class="px-4 py-2 text-muted-foreground truncate max-w-[200px]">{{ $item->reason ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 font-medium">
                        <td colspan="4" class="px-4 py-2 text-right text-muted-foreground">合计</td>
                        <td class="px-4 py-2 text-right text-foreground font-bold">{{ money_format($lossOrder->total_amount) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="px-4 py-8 text-center text-sm text-muted-foreground">暂无损耗明细</div>
        @endif
    </div>

    {{-- Tab 内容：状态变更记录 --}}
    <div x-show="activeTab === 'logs'" x-transition>
        @if($lossOrder->auditLogs->isNotEmpty())
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
                    @foreach($lossOrder->auditLogs->sortByDesc('created_at') as $log)
                    <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2 text-muted-foreground tabular-nums">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium
                                @if($log->action === 'cancel') bg-red-100 text-red-700
                                @elseif($log->action === 'rollback') bg-purple-100 text-purple-700
                                @elseif($log->action === 'status_change') bg-blue-100 text-blue-700
                                @elseif($log->action === 'create') bg-green-100 text-green-700
                                @elseif($log->action === 'approve') bg-green-100 text-green-700
                                @elseif($log->action === 'reject') bg-red-100 text-red-700
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
                            @endphp
                            @if($beforeStatus && $afterStatus)
                                {!! status_badge($beforeStatus, 'loss_status') !!}
                                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5 inline text-muted-foreground mx-1" />
                                {!! status_badge($afterStatus, 'loss_status') !!}
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

    {{-- 审核弹窗 --}}
    @if($showApproveConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">审核损耗单</h2>
                <button type="button" wire:click="closeApproveConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">请选择审核结果</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeApproveConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="reject" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">拒绝</button>
                <button type="button" wire:click="approve" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">通过</button>
            </div>
        </div>
    </div>
    @endif
</div>
