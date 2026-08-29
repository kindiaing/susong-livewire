<div class="" x-data="{ activeTab: 'info' }">
    {{-- 顶部：返回 + 商家名 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('merchant-accounts') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $account->merchant?->name ?? '-' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            @can('finance.merchant-account.edit')
            @if($account->canEdit())
                <a href="{{ route('merchant-accounts') }}?edit={{ $account->id }}" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">编辑</a>
            @endif
            @endcan
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>账户ID：<b class="text-foreground">{{ $account->id }}</b></span>
        <span>商家ID：<b class="text-foreground">{{ $account->merchant_id }}</b></span>
        <span>创建时间：<b class="text-foreground">{{ $account->created_at?->format('Y-m-d H:i') }}</b></span>
        <span>更新时间：<b class="text-foreground">{{ $account->updated_at?->format('Y-m-d H:i') }}</b></span>
    </div>

    {{-- Tab 切换 --}}
    <div class="border-b mb-4">
        <nav class="flex gap-6">
            <button type="button" @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                账户信息
            </button>
            <button type="button" @click="activeTab = 'recharges'" :class="activeTab === 'recharges' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                充值记录
                @if($recentRecharges->count() > 0)
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-blue-100 text-blue-700 text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $recentRecharges->count() }}</span>
                @endif
            </button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                状态变更记录
                @if($auditLogs->count() > 0)
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-muted text-muted-foreground text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $auditLogs->count() }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab 内容：账户信息（余额 + 资金汇总） --}}
    <div x-show="activeTab === 'info'">
        {{-- 余额信息 --}}
        <div class="rounded-lg border bg-card mb-6">
            <div class="px-4 py-2 border-b">
                <h2 class="text-sm font-semibold text-foreground">账户余额</h2>
            </div>
            <div class="px-4 py-3 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">账户余额</span>
                    <span class="text-foreground font-medium">{{ money_format($account->balance) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">− 冻结金额</span>
                    <span class="text-orange-600 font-medium">− {{ money_format($account->frozen_amount) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-foreground font-medium">可用余额</span>
                    <span class="@if($availableBalance > 0) text-green-600 @else text-red-600 @endif font-bold text-base">{{ money_format($availableBalance) }}</span>
                </div>
            </div>
        </div>

        {{-- 资金汇总 --}}
        <div class="rounded-lg border bg-card">
            <div class="px-4 py-2 border-b">
                <h2 class="text-sm font-semibold text-foreground">资金汇总</h2>
            </div>
            <div class="px-4 py-3 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">总充值</span>
                    <span class="text-green-600 font-medium">{{ money_format($account->total_recharge) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">总消费</span>
                    <span class="text-orange-600 font-medium">{{ money_format($account->total_consumption) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-muted-foreground">信用额度</span>
                    <span class="text-foreground font-medium">{{ money_format($account->credit_limit) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab 内容：充值记录 --}}
    <div x-show="activeTab === 'recharges'" x-transition>
        <div class="rounded-lg border bg-card">
            <div class="px-4 py-2 border-b">
                <h2 class="text-sm font-semibold text-foreground">最近充值记录（{{ $recentRecharges->count() }}）</h2>
            </div>
            @if($recentRecharges->count())
            <div class="divide-y">
                @foreach($recentRecharges as $recharge)
                <div class="px-4 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full @if($recharge->status === 2) bg-green-500 @elseif($recharge->status === 1) bg-yellow-500 @else bg-red-500 @endif"></span>
                        <div>
                            <div class="text-sm text-foreground font-mono">
                                <a href="{{ route('recharges.detail', $recharge->id) }}" class="text-blue-600 hover:underline">{{ $recharge->transaction_no }}</a>
                            </div>
                            <div class="text-xs text-muted-foreground mt-0.5">
                                {{ \App\Models\Recharge::paymentMethodMap()[$recharge->payment_method] ?? '-' }}
                                · {{ $recharge->created_at?->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>
                    <span class="text-sm text-foreground font-medium">{{ money_format($recharge->amount) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-4 py-8 text-center text-sm text-muted-foreground">暂无充值记录</div>
            @endif
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
                            @endphp
                            @if(!empty($before) || !empty($after))
                                <span class="text-xs text-muted-foreground">{{ json_encode(array_diff_key($after ?? [], $before ?? []), JSON_UNESCAPED_UNICODE) }}</span>
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
