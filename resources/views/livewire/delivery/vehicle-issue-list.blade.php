<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">车辆故障</h1>
            <p class="text-muted-foreground mt-1">车辆故障记录与处理跟踪</p>
        </div>
        @can('delivery.vehicle-issue.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增故障
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索描述/车牌号..." />
            @if($search)
                <button type="button" wire:click="$set('search','')" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-28 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部状态</option>
            @foreach(\App\Livewire\Delivery\VehicleIssueList::$statusMap as $val => $label)
            <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterVehicleId" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部车辆</option>
            @foreach($vehicleOptions as $id => $plate)
            <option value="{{ $id }}">{{ $plate }}</option>
            @endforeach
        </select>
        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('delivery.vehicle-issue.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $statusMap = \App\Livewire\Delivery\VehicleIssueList::$statusMap;
        $statusColorMap = \App\Livewire\Delivery\VehicleIssueList::$statusColorMap;
        $issueTypeMap = \App\Livewire\Delivery\VehicleIssueList::$issueTypeMap;
        $impactTypeMap = \App\Livewire\Delivery\VehicleIssueList::$impactTypeMap;
        $allCols = collect($this->getAllColumns());
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 180px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-2 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="vehicle-issue-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('vehicle')
                            <div class="text-sm text-foreground">{{ $item->vehicle?->plate_number ?? '-' }}</div>
                            @break
                        @case('issue_type')
                            <div class="text-sm text-foreground">{{ $issueTypeMap[$item->issue_type] ?? $item->issue_type }}</div>
                            @break
                        @case('description')
                            <div class="text-sm text-foreground truncate max-w-[200px]">{{ $item->description }}</div>
                            @break
                        @case('status')
                            <div>
                                @php $c = $statusColorMap[$item->status] ?? 'gray'; @endphp
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ $statusMap[$item->status] ?? '-' }}</span>
                            </div>
                            @break
                        @case('impact_type')
                            <div class="text-sm text-foreground">{{ $impactTypeMap[$item->impact_type] ?? ($item->impact_type ?: '-') }}</div>
                            @break
                        @case('reported_at')
                            <div class="text-sm text-foreground">{{ $item->reported_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            @break
                        @case('resolved_at')
                            <div class="text-sm text-foreground">{{ $item->resolved_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('delivery.vehicle-issue.edit')
                    <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endcan
                    @can('delivery.vehicle-issue.edit')
                    @if($item->status === \App\Models\VehicleIssue::STATUS_OPEN)
                    <button type="button" wire:click="resolveIssue({{ $item->id }})" class="text-green-600 hover:text-green-700 text-sm">标记解决</button>
                    @endif
                    @endcan
                    @can('delivery.vehicle-issue.edit')
                    @if(in_array($item->status, [\App\Models\VehicleIssue::STATUS_OPEN, \App\Models\VehicleIssue::STATUS_RESOLVED]))
                    <button type="button" wire:click="closeIssue({{ $item->id }})" class="text-orange-600 hover:text-orange-700 text-sm">关闭</button>
                    @endif
                    @endcan
                    @can('delivery.vehicle-issue.delete')
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无故障记录</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑故障记录' : '新增故障记录' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">车辆 <span class="text-red-500">*</span></label>
                    <select wire:model="formVehicleId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择车辆</option>
                        @foreach($vehicleOptions as $id => $plate)
                        <option value="{{ $id }}">{{ $plate }}</option>
                        @endforeach
                    </select>
                    @error('formVehicleId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">故障类型 <span class="text-red-500">*</span></label>
                    <select wire:model="formIssueType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @foreach($issueTypeMap as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('formIssueType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">描述 <span class="text-red-500">*</span></label>
                    <textarea wire:model="formDescription" rows="3" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="故障描述..."></textarea>
                    @error('formDescription') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">影响类型</label>
                    <select wire:model="formImpactType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">无</option>
                        @foreach($impactTypeMap as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">影响描述</label>
                    <textarea wire:model="formImpactDesc" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="影响详情..."></textarea>
                    @error('formImpactDesc') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingId ? '更新' : '创建' }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认删除</h2>
                <button type="button" wire:click="closeDeleteConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该故障记录吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">确认删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 列配置弹窗 --}}
    @if($showColumnModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">列配置</h2>
                <button type="button" wire:click="closeColumnModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-2">
                @foreach($allColumns as $col)
                <label class="flex items-center gap-2 text-sm text-foreground cursor-pointer hover:bg-muted/50 rounded px-2 py-1.5 transition-colors">
                    <input type="checkbox" value="{{ $col['key'] }}" wire:model.live="visibleColumns" class="rounded" />
                    {{ $col['label'] }}
                </label>
                @endforeach
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="resetColumns" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">恢复默认</button>
                <button type="button" wire:click="closeColumnModal" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确定</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 导出弹窗 --}}
    @if($showExportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">导出 Excel</h2>
                <button type="button" wire:click="closeExportModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-2">
                @foreach($allColumns as $col)
                @if(!empty($col['exportable']))
                <label class="flex items-center gap-2 text-sm text-foreground cursor-pointer hover:bg-muted/50 rounded px-2 py-1.5 transition-colors">
                    <input type="checkbox" value="{{ $col['key'] }}" wire:model.live="exportColumns" class="rounded" />
                    {{ $col['label'] }}
                </label>
                @endif
                @endforeach
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeExportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="export" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">导出</button>
            </div>
        </div>
    </div>
    @endif
</div>