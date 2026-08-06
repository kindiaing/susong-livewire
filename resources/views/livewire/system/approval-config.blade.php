<div class="">
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
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left">审核节点</th>
                    <th class="px-4 py-2 text-left">模块</th>
                    <th class="px-4 py-2 text-left">风险</th>
                    <th class="px-4 py-2 text-left">申请人角色</th>
                    <th class="px-4 py-2 text-left">审核人角色</th>
                    <th class="px-4 py-2 text-left">状态</th>
                    <th class="px-4 py-2 text-right w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($configs as $config)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="atc-{{ $config->id }}">
                    {{-- 节点名称 --}}
                    <td class="px-4 py-3">
                        <div class="min-w-0">
                            <div class="font-medium text-foreground">{{ $config->type_name }}</div>
                            @if($this->editingId === $config->id)
                                <div class="mt-1 flex items-center gap-2">
                                    <input type="text" wire:model="editDescription"
                                           class="flex h-7 flex-1 rounded-md border border-input bg-background px-2 text-xs"
                                           placeholder="输入节点说明">
                                    <button type="button" wire:click="saveDescription" class="text-xs text-blue-600 hover:text-blue-700 font-medium">保存</button>
                                    <button type="button" wire:click="cancelEdit" class="text-xs text-muted-foreground hover:text-foreground">取消</button>
                                </div>
                            @else
                                <div class="flex items-center gap-1 mt-0.5">
                                    <p class="text-xs text-muted-foreground">{{ $config->description ?? '暂无说明' }}</p>
                                    <button type="button" wire:click="startEdit({{ $config->id }})" class="text-xs text-muted-foreground hover:text-foreground opacity-0 group-hover:opacity-100">
                                        <x-ui.icon name="pencil-square" class="w-3 h-3" />
                                    </button>
                                </div>
                            @endif
                            <p class="text-[11px] text-muted-foreground/60 font-mono mt-0.5">{{ $config->type_code }}</p>
                        </div>
                    </td>

                    {{-- 模块 --}}
                    <td class="px-4 py-3">
                        <span class="text-foreground">{{ $config->module_name }}</span>
                    </td>

                    {{-- 风险等级 --}}
                    <td class="px-4 py-3">
                        <x-ui.badge variant="{{ $config->risk_level === 'P0' ? 'red' : 'orange' }}">
                            {{ $config->risk_level }}
                        </x-ui.badge>
                    </td>

                    {{-- 申请人角色 --}}
                    <td class="px-4 py-3 text-muted-foreground">
                        {{ $config->applicantRole?->display_name ?? '-' }}
                    </td>

                    {{-- 审核人角色 --}}
                    <td class="px-4 py-3 text-muted-foreground">
                        {{ $config->reviewerRole?->display_name ?? '-' }}
                    </td>

                    {{-- 状态 --}}
                    <td class="px-4 py-3">
                        {!! status_badge($config->enabled ? 1 : 0, 'active') !!}
                    </td>

                    {{-- 操作 --}}
                    <td class="px-4 py-3 text-right">
                        <x-ui.alert-dialog
                            :title="$config->enabled ? '确认关闭审核节点' : '确认开启审核节点'"
                            :description="$config->enabled ? '关闭后相关操作将无需审批，确定要关闭吗？' : '开启后相关操作需要审核人审批，确定要开启吗？'"
                            :confirmText="$config->enabled ? '确认关闭' : '确认开启'"
                            :variant="$config->enabled ? 'destructive' : 'warning'"
                            :confirmAction="'$wire.toggleEnabled(' . $config->id . ')'"
                        >
                            <button type="button" class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium transition-colors {{ $config->enabled ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}"
                            >
                                {{ $config->enabled ? '关闭' : '开启' }}
                            </button>
                        </x-ui.alert-dialog>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无审核节点配置</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
