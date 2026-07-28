<div class="p-6">
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">审核管理</h1>
        <p class="text-muted-foreground mt-1">管理审核节点开关，配置各业务环节的审批流程</p>
    </div>

    {{-- 提示消息 --}}
    @if(session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- 统计卡片 --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="rounded-lg border bg-card p-4">
            <div class="text-sm text-muted-foreground">总节点</div>
            <div class="text-2xl font-bold text-foreground mt-1">{{ $totalCount }}</div>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <div class="text-sm text-muted-foreground">已启用</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $enabledCount }}</div>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <div class="text-sm text-muted-foreground">已关闭</div>
            <div class="text-2xl font-bold text-muted-foreground mt-1">{{ $totalCount - $enabledCount }}</div>
        </div>
    </div>

    {{-- 筛选栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <select wire:model="filterModule" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部模块</option>
            @foreach($modules as $module)
                <option value="{{ $module }}">{{ $module }}</option>
            @endforeach
        </select>
        <select wire:model="filterRisk" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部等级</option>
            <option value="P0">P0 核心资金</option>
            <option value="P1">P1 一般业务</option>
        </select>
    </div>

    {{-- 配置列表 --}}
    <div class="rounded-lg border bg-card">
        {{-- 表头 --}}
        <div class="grid grid-cols-[2fr_1fr_80px_1fr_1fr_60px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>审核节点</div>
            <div>模块</div>
            <div>风险</div>
            <div>申请人角色</div>
            <div>审核人角色</div>
            <div>状态</div>
            <div class="text-right">操作</div>
        </div>

        {{-- 配置行 --}}
        @forelse($configs as $config)
            <div class="grid grid-cols-[2fr_1fr_80px_1fr_1fr_60px_100px] gap-3 border-b last:border-b-0 px-6 py-4 items-center hover:bg-muted/30 transition-colors"
                 wire:key="atc-{{ $config->id }}">
                {{-- 节点名称 --}}
                <div class="min-w-0">
                    <div class="text-sm font-medium text-foreground">{{ $config->type_name }}</div>
                    @if($this->editingId === $config->id)
                        <div class="mt-1 flex items-center gap-2">
                            <input type="text" wire:model="editDescription"
                                   class="flex h-7 flex-1 rounded-md border border-input bg-background px-2 text-xs"
                                   placeholder="输入节点说明">
                            <button wire:click="saveDescription" class="text-xs text-blue-600 hover:text-blue-700 font-medium">保存</button>
                            <button wire:click="cancelEdit" class="text-xs text-muted-foreground hover:text-foreground">取消</button>
                        </div>
                    @else
                        <div class="flex items-center gap-1 mt-0.5">
                            <p class="text-xs text-muted-foreground">{{ $config->description ?? '暂无说明' }}</p>
                            <button wire:click="startEdit({{ $config->id }})" class="text-xs text-muted-foreground hover:text-foreground opacity-0 group-hover:opacity-100">
                                <x-ui.icon name="pencil" class="w-3 h-3" />
                            </button>
                        </div>
                    @endif
                    <p class="text-[11px] text-muted-foreground/60 font-mono mt-0.5">{{ $config->type_code }}</p>
                </div>

                {{-- 模块 --}}
                <div>
                    <span class="text-sm text-foreground">{{ $config->module_name }}</span>
                </div>

                {{-- 风险等级 --}}
                <div>
                    <x-ui.badge variant="{{ $config->risk_level === 'P0' ? 'red' : 'orange' }}">
                        {{ $config->risk_level }}
                    </x-ui.badge>
                </div>

                {{-- 申请人角色 --}}
                <div class="text-sm text-muted-foreground">
                    {{ $config->applicantRole?->display_name ?? '-' }}
                </div>

                {{-- 审核人角色 --}}
                <div class="text-sm text-muted-foreground">
                    {{ $config->reviewerRole?->display_name ?? '-' }}
                </div>

                {{-- 状态 --}}
                <div>
                    @if($config->enabled)
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">启用</span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">关闭</span>
                    @endif
                </div>

                {{-- 操作 --}}
                <div class="text-right">
                    <x-ui.alert-dialog
                        :title="$config->enabled ? '确认关闭审核节点' : '确认开启审核节点'"
                        :description="$config->enabled ? '关闭后相关操作将无需审批，确定要关闭吗？' : '开启后相关操作需要审核人审批，确定要开启吗？'"
                        :confirmText="$config->enabled ? '确认关闭' : '确认开启'"
                        :variant="$config->enabled ? 'destructive' : 'warning'"
                        :confirmAction="'$wire.toggleEnabled(' . $config->id . ')'"
                    >
                        <button
                            class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium transition-colors {{ $config->enabled ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}"
                        >
                            {{ $config->enabled ? '关闭' : '开启' }}
                        </button>
                    </x-ui.alert-dialog>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">
                暂无审核节点配置
            </div>
        @endforelse
    </div>
</div>
