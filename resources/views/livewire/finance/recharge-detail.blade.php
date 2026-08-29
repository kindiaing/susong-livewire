<div class="" x-data="{ activeTab: 'info' }">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('recharges') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $recharge->transaction_no }}</h1>
            @php $sc = $statusColorMap[$recharge->status] ?? 'gray'; @endphp
            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ $statusMap[$recharge->status] ?? '-' }}</span>
        </div>
        <div class="flex items-center gap-2">
            @can('finance.recharge.create')
            @if($recharge->status === \App\Models\Recharge::STATUS_PENDING)
                <button type="button" wire:click="approve" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors" onclick="return confirm('确认通过此充值？金额将入账商家余额。')">通过</button>
                <button type="button" wire:click="reject" class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">拒绝</button>
            @endif
            @if($recharge->status === \App\Models\Recharge::STATUS_SUCCESS)
                <button type="button" wire:click="confirmArrival" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">确认到账</button>
            @endif
            @endcan
            @can('finance.recharge.edit')
            @if($recharge->canEdit())
                <a href="{{ route('recharges') }}?edit={{ $recharge->id }}" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">编辑</a>
            @endif
            @endcan
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>商家：<b class="text-foreground">{{ $recharge->merchant?->name ?? '-' }}</b></span>
        <span>支付方式：<b class="text-foreground">{{ $paymentMethodMap[$recharge->payment_method] ?? '-' }}</b></span>
        @if($recharge->operator)
            <span>操作人：<b class="text-foreground">{{ $recharge->operator->name }}</b></span>
        @endif
        <span>创建时间：<b class="text-foreground">{{ $recharge->created_at?->format('Y-m-d H:i') }}</b></span>
        @if($recharge->remark)
            <span>备注：<b class="text-foreground">{{ $recharge->remark }}</b></span>
        @endif
    </div>

    {{-- Tab 切换 --}}
    <div class="border-b mb-4">
        <nav class="flex gap-6">
            <button type="button" @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                充值信息
            </button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                状态变更记录
                @if($auditLogs->count() > 0)
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-muted text-muted-foreground text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $auditLogs->count() }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab 内容：充值信息（金额 + 审核信息） --}}
    <div x-show="activeTab === 'info'">
        {{-- 金额信息 --}}
        <div class="rounded-lg border bg-card mb-6">
            <div class="px-4 py-2 border-b">
                <h2 class="text-sm font-semibold text-foreground">充值金额</h2>
            </div>
            <div class="px-4 py-3 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">充值金额</span>
                    <span class="text-foreground font-bold text-base">{{ money_format($recharge->amount) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-muted-foreground">入账状态</span>
                    @if($recharge->status === \App\Models\Recharge::STATUS_SUCCESS)
                        <span class="text-green-600 font-medium">已入账</span>
                    @elseif($recharge->status === \App\Models\Recharge::STATUS_PENDING)
                        <span class="text-yellow-600 font-medium">待确认</span>
                    @else
                        <span class="text-red-600 font-medium">未入账</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 审核信息 --}}
        <div class="rounded-lg border bg-card">
            <div class="px-4 py-2 border-b">
                <h2 class="text-sm font-semibold text-foreground">审核信息</h2>
            </div>
            <div class="px-4 py-3 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">审核状态</span>
                    @php
                        $approvalLabels = [1 => '待审核', 2 => '已通过', 3 => '已拒绝'];
                        $approvalColors = [1 => 'yellow', 2 => 'green', 3 => 'red'];
                        $ac = $approvalColors[$recharge->approval_status] ?? 'gray';
                    @endphp
                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $ac }}-100 text-{{ $ac }}-700">{{ $approvalLabels[$recharge->approval_status] ?? '-' }}</span>
                </div>
                @if($recharge->remark)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">备注</span>
                    <span class="text-foreground text-right max-w-[400px]">{{ $recharge->remark }}</span>
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
                                @elseif($log->action === 'reject') bg-red-100 text-red-700
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
                                $statusMap = [
                                    \App\Models\Recharge::STATUS_PENDING => ['label' => '待确认', 'color' => 'yellow'],
                                    \App\Models\Recharge::STATUS_SUCCESS => ['label' => '成功', 'color' => 'green'],
                                    \App\Models\Recharge::STATUS_FAILED => ['label' => '失败', 'color' => 'red'],
                                ];
                            @endphp
                            @if($beforeStatus && $afterStatus && isset($statusMap[$beforeStatus]) && isset($statusMap[$afterStatus]))
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $statusMap[$beforeStatus]['color'] }}-100 text-{{ $statusMap[$beforeStatus]['color'] }}-700">{{ $statusMap[$beforeStatus]['label'] }}</span>
                                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5 inline text-muted-foreground mx-1" />
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $statusMap[$afterStatus]['color'] }}-100 text-{{ $statusMap[$afterStatus]['color'] }}-700">{{ $statusMap[$afterStatus]['label'] }}</span>
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
