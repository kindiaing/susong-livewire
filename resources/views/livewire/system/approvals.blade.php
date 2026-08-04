<div class="">
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">审批列表</h1>
        <p class="text-muted-foreground mt-1">查看和管理所有审批记录，审核人可在此通过或拒绝审批</p>
    </div>

    {{-- 提示消息 --}}
    @if(session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tab 切换 --}}
    <div class="flex items-center gap-1 mb-4 border-b">
        @php
            $tabs = [
                'pending' => ['label' => '待审核', 'count' => $pendingCount],
                'approved' => ['label' => '已通过', 'count' => null],
                'rejected' => ['label' => '已拒绝', 'count' => null],
                'all' => ['label' => '全部', 'count' => null],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <button type="button" wire:click="setActiveTab('{{ $key }}')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ $activeTab === $key ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground' }}"
            >
                {{ $tab['label'] }}
                @if($tab['count'] !== null && $tab['count'] > 0)
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-red-600 text-white text-[10px] font-medium w-5 h-5">{{ $tab['count'] }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- 筛选 --}}
    <div class="flex items-center gap-3 mb-4">
        <select wire:model="filterType" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部类型</option>
            @foreach($approvalTypes as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>

    {{-- 审批列表 --}}
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left">审批信息</th>
                    <th class="px-4 py-2 text-left">类型</th>
                    <th class="px-4 py-2 text-left">申请人</th>
                    <th class="px-4 py-2 text-left">涉及金额</th>
                    <th class="px-4 py-2 text-left">时间</th>
                    <th class="px-4 py-2 text-right">状态</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvals as $approval)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors cursor-pointer"
                     wire:click="showDetail({{ $approval->id }})"
                     wire:key="approval-{{ $approval->id }}">
                    {{-- 审批信息 --}}
                    <td class="px-4 py-3">
                        <div class="min-w-0">
                            <div class="font-medium text-foreground truncate">{{ $approval->typeConfig?->type_name ?? $approval->approval_type }}</div>
                            <div class="text-xs text-muted-foreground mt-0.5 truncate">
                                {{ $approval->target_type }} #{{ $approval->target_id }}
                            </div>
                        </div>
                    </td>

                    {{-- 类型编码 --}}
                    <td class="px-4 py-3 text-muted-foreground">{{ $approval->approval_type }}</td>

                    {{-- 申请人 --}}
                    <td class="px-4 py-3 text-foreground">{{ $approval->applicant_name }}</td>

                    {{-- 金额 --}}
                    <td class="px-4 py-3 font-mono text-foreground">
                        @if($approval->amount)
                            {{ money_format($approval->amount) }}
                        @else
                            -
                        @endif
                    </td>

                    {{-- 时间 --}}
                    <td class="px-4 py-3 text-muted-foreground">
                        {{ $approval->created_at?->format('Y-m-d H:i') }}
                    </td>

                    {{-- 状态 --}}
                    <td class="px-4 py-3 text-right">
                        @php $color = $approval->status_color; @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $color === 'green' ? 'bg-green-100 text-green-700' : ($color === 'red' ? 'bg-red-100 text-red-700' : ($color === 'orange' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600')) }}">
                            {{ $approval->status_label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-muted-foreground">暂无审批记录</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 分页 --}}
    <div class="mt-4">
        {{ $approvals->links() }}
    </div>

    {{-- 详情弹窗 --}}
    @if($detailApproval)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
            <div class="relative z-10 w-full max-w-2xl mx-4 rounded-lg border bg-background shadow-lg max-h-[85vh] overflow-y-auto"
                 wire:click.stop>
                {{-- 弹窗头 --}}
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <h2 class="text-lg font-semibold text-foreground">审批详情</h2>
                    <button type="button" wire:click="closeDetail" class="p-1 rounded-md hover:bg-muted text-muted-foreground">
                        <x-ui.icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                {{-- 弹窗内容 --}}
                <div class="px-6 py-4 space-y-4">
                    {{-- 基本信息 --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">审核类型</div>
                            <div class="text-sm font-medium">{{ $detailApproval->typeConfig?->type_name ?? $detailApproval->approval_type }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">状态</div>
                            <div>
                                @php $sc = $detailApproval->status_color; @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $sc === 'green' ? 'bg-green-100 text-green-700' : ($sc === 'red' ? 'bg-red-100 text-red-700' : ($sc === 'orange' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ $detailApproval->status_label }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">申请人</div>
                            <div class="text-sm">{{ $detailApproval->applicant_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">申请时间</div>
                            <div class="text-sm">{{ $detailApproval->created_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">关联单据</div>
                            <div class="text-sm">{{ $detailApproval->target_type }} #{{ $detailApproval->target_id }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">涉及金额</div>
                            <div class="text-sm font-mono">
                                @if($detailApproval->amount)
                                    {{ money_format($detailApproval->amount) }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 数据快照 --}}
                    @if($detailApproval->before_data || $detailApproval->after_data)
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">数据变更</div>
                            <div class="grid grid-cols-2 gap-3">
                                @if($detailApproval->before_data)
                                    <div class="rounded-md border bg-red-50 p-3">
                                        <div class="text-xs font-medium text-red-700 mb-1">修改前</div>
                                        <pre class="text-xs text-red-600 whitespace-pre-wrap break-all">{{ json_encode($detailApproval->before_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                @endif
                                @if($detailApproval->after_data)
                                    <div class="rounded-md border bg-green-50 p-3">
                                        <div class="text-xs font-medium text-green-700 mb-1">修改后</div>
                                        <pre class="text-xs text-green-600 whitespace-pre-wrap break-all">{{ json_encode($detailApproval->after_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- 审核信息 --}}
                    @if($detailApproval->reviewer_id)
                        <div class="rounded-md border bg-muted/30 p-4">
                            <div class="text-xs text-muted-foreground mb-2">审核信息</div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-muted-foreground">审核人：</span>
                                    <span class="font-medium">{{ $detailApproval->reviewer_name }}</span>
                                </div>
                                <div>
                                    <span class="text-muted-foreground">审核时间：</span>
                                    <span>{{ $detailApproval->reviewed_at?->format('Y-m-d H:i:s') }}</span>
                                </div>
                                @if($detailApproval->review_remark)
                                    <div class="col-span-2">
                                        <span class="text-muted-foreground">审核备注：</span>
                                        <span>{{ $detailApproval->review_remark }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- 审核操作（仅待审核时显示） --}}
                    @if($detailApproval->status === \App\Models\Approval::STATUS_PENDING)
                        <div class="border-t pt-4">
                            <div class="mb-3">
                                <label class="text-sm font-medium text-foreground mb-1 block">审核备注</label>
                                <textarea
                                    wire:model="reviewRemark"
                                    class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm min-h-[80px]"
                                    placeholder="填写审核备注（拒绝时必填）"
                                ></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.alert-dialog
                                    title="确认撤回"
                                    description="撤回后该审批申请将被取消，确定要撤回吗？"
                                    confirmText="确认撤回"
                                    variant="info"
                                    :confirmAction="'$wire.withdraw(' . $detailApproval->id . ')'"
                                >
                                    <button type="button" class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium bg-muted text-muted-foreground hover:bg-muted/80 transition-colors"
                                    >
                                        撤回
                                    </button>
                                </x-ui.alert-dialog>

                                <x-ui.alert-dialog
                                    title="确认拒绝"
                                    description="拒绝后申请人需要重新发起审批，确定要拒绝吗？"
                                    confirmText="确认拒绝"
                                    variant="destructive"
                                    :confirmAction="'$wire.reject(' . $detailApproval->id . ')'"
                                >
                                    <button type="button" class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium bg-red-600 text-white hover:bg-red-700 transition-colors"
                                    >
                                        拒绝
                                    </button>
                                </x-ui.alert-dialog>

                                <x-ui.alert-dialog
                                    title="确认通过"
                                    description="通过后该审批将立即生效，确定要通过吗？"
                                    confirmText="确认通过"
                                    variant="warning"
                                    :confirmAction="'$wire.approve(' . $detailApproval->id . ')'"
                                >
                                    <button type="button" class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition-colors"
                                    >
                                        通过
                                    </button>
                                </x-ui.alert-dialog>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
