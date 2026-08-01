<div class="p-6">
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">审计日志</h1>
        <p class="text-muted-foreground mt-1">敏感操作审计记录，包含数据变更前后快照</p>
    </div>

    {{-- 筛选栏 --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <select wire:model="filterAction" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部动作</option>
            @foreach($actions as $action)
                <option value="{{ $action }}">
                    @php $label = \App\Models\AuditLog::actionMap()[$action] ?? $action; @endphp
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <select wire:model="filterModelType" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部模型</option>
            @foreach($modelTypes as $mt)
                <option value="{{ $mt }}">{{ $mt }}</option>
            @endforeach
        </select>

        <input
            type="text"
            wire:model="filterOperator"
            class="flex h-9 w-40 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="操作人"
        />

        <input
            type="date"
            wire:model="filterDateStart"
            class="flex h-9 rounded-md border border-input bg-background px-3 text-sm"
        />
        <span class="text-sm text-muted-foreground">至</span>
        <input
            type="date"
            wire:model="filterDateEnd"
            class="flex h-9 rounded-md border border-input bg-background px-3 text-sm"
        />

        <button type="button" wire:click="s" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
            重置
        </button>
    </div>

    {{-- 日志列表 --}}
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[1fr_100px_1fr_120px_150px_120px_60px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>模型</div>
            <div>动作</div>
            <div>原因/关联</div>
            <div>操作人</div>
            <div>IP</div>
            <div>时间</div>
            <div class="text-right">详情</div>
        </div>

        @forelse($logs as $log)
            <div class="grid grid-cols-[1fr_100px_1fr_120px_150px_120px_60px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="alog-{{ $log->id }}">
                {{-- 模型 --}}
                <div class="min-w-0">
                    <div class="text-sm text-foreground truncate">{{ $log->model_type }}</div>
                    <div class="text-xs text-muted-foreground font-mono">ID: {{ $log->model_id }}</div>
                </div>

                {{-- 动作 --}}
                <div>
                    @php
                        $actionLabel = $log->action_label;
                        $actionColors = [
                            'create' => 'bg-green-100 text-green-700',
                            'update' => 'bg-blue-100 text-blue-700',
                            'delete' => 'bg-red-100 text-red-700',
                            'approve' => 'bg-green-100 text-green-700',
                            'reject' => 'bg-red-100 text-red-700',
                        ];
                        $actionColor = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium {{ $actionColor }}">
                        {{ $actionLabel }}
                    </span>
                </div>

                {{-- 原因/关联 --}}
                <div class="min-w-0">
                    @if($log->reason)
                        <div class="text-sm text-foreground truncate">{{ $log->reason }}</div>
                    @endif
                    @if($log->relation_type)
                        <div class="text-xs text-muted-foreground truncate">{{ $log->relation_type }} #{{ $log->relation_id }}</div>
                    @endif
                    @if(!$log->reason && !$log->relation_type)
                        <span class="text-sm text-muted-foreground">-</span>
                    @endif
                </div>

                {{-- 操作人 --}}
                <div class="text-sm text-foreground">{{ $log->operator?->username ?? ($log->operator_id ? "ID:{$log->operator_id}" : '-') }}</div>

                {{-- IP --}}
                <div class="text-sm text-muted-foreground font-mono truncate">{{ $log->ip ?? '-' }}</div>

                {{-- 时间 --}}
                <div class="text-sm text-muted-foreground">{{ $log->created_at?->format('Y-m-d H:i') }}</div>

                {{-- 详情按钮 --}}
                <div class="text-right">
                    @if($log->before_data || $log->after_data)
                        <button type="button" wire:click=")"
                            class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                        >
                            查看
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">
                暂无审计日志
            </div>
        @endforelse
    </div>

    {{-- 分页 --}}
    <div class="mt-4">
        {{ $logs->links() }}
    </div>

    {{-- 详情弹窗 --}}
    @if($detailLog)
        <div class="fixed inset-0 z-50 flex items-center justify-center" wire:click="closeDetail">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="relative z-10 w-full max-w-2xl mx-4 rounded-lg border bg-background shadow-lg max-h-[85vh] overflow-y-auto"
                 wire:click.stop>
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <h2 class="text-lg font-semibold text-foreground">审计详情</h2>
                    <button type="button" wire:click="l" class="p-1 rounded-md hover:bg-muted text-muted-foreground">
                        <x-ui.icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-muted-foreground">模型：</span>
                            <span class="font-medium">{{ $detailLog->model_type }}</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">ID：</span>
                            <span class="font-mono">{{ $detailLog->model_id }}</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">动作：</span>
                            <span class="font-medium">{{ $detailLog->action_label }}</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">操作人：</span>
                            <span>{{ $detailLog->operator?->username ?? ($detailLog->operator_id ?? '-') }}</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">IP：</span>
                            <span class="font-mono">{{ $detailLog->ip ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">时间：</span>
                            <span>{{ $detailLog->created_at?->format('Y-m-d H:i:s') }}</span>
                        </div>
                    </div>

                    @if($detailLog->reason)
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">操作原因</div>
                            <div class="text-sm bg-muted/50 rounded p-3">{{ $detailLog->reason }}</div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        @if($detailLog->before_data)
                            <div class="rounded-md border bg-red-50 p-3">
                                <div class="text-xs font-medium text-red-700 mb-2">修改前</div>
                                <pre class="text-xs text-red-600 whitespace-pre-wrap break-all">{{ json_encode($detailLog->before_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                        @if($detailLog->after_data)
                            <div class="rounded-md border bg-green-50 p-3">
                                <div class="text-xs font-medium text-green-700 mb-2">修改后</div>
                                <pre class="text-xs text-green-600 whitespace-pre-wrap break-all">{{ json_encode($detailLog->after_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>

                    @if($detailLog->user_agent)
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">User Agent</div>
                            <div class="text-xs text-muted-foreground break-all">{{ $detailLog->user_agent }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
