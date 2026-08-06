<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">车辆管理</h1>
            <p class="text-muted-foreground mt-1">管理车辆信息及冷链标记</p>
        </div>
        @can('org.vehicle.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增车辆
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索车牌号/车辆类型..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select
            wire:model.live="filterStatus"
            class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm"
        >
            <option value="">全部状态</option>
            <option value="1">启用</option>
            <option value="0">禁用</option>
        </select>
        <select
            wire:model.live="filterIsColdChain"
            class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm"
        >
            <option value="">全部冷链</option>
            <option value="1">冷链</option>
            <option value="0">非冷链</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('org.vehicle.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 车辆列表 --}}
    @php
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'plate_number')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 120px';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 100px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAll" class="rounded" /></div>
            <div>车牌号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($vehicles as $vehicle)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="vehicle-{{ $vehicle->id }}">
                <div><input type="checkbox" value="{{ $vehicle->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-medium text-foreground font-mono">{{ $vehicle->plate_number }}</div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $vehicle->id }}</div>
                            @break
                        @case('type')
                            <div class="text-sm text-foreground">{{ $vehicle->vehicle_type ?? '-' }}</div>
                            @break
                        @case('is_cold_chain')
                            <div>
                                {!! status_badge($vehicle->is_cold_chain, 'cold_chain') !!}
                            </div>
                            @break
                        @case('status')
                            <div>
                                {!! status_badge($vehicle->status, 'active') !!}
                            </div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $vehicle->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $vehicle->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('org.vehicle.edit')
                    <button type="button" wire:click="openEditModal({{ $vehicle->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endcan
                    @can('org.vehicle.delete')
                    <button type="button" wire:click="confirmDelete({{ $vehicle->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无车辆数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $vehicles->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑车辆' : '新增车辆' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">车牌号 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formPlateNumber" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 京A12345（唯一）" />
                    @error('formPlateNumber') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">车辆类型</label>
                    <input type="text" wire:model="formVehicleType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 冷藏车、厢式货车" />
                    @error('formVehicleType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">冷链车辆 <span class="text-red-500">*</span></label>
                        <select wire:model="formIsColdChain" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                        @error('formIsColdChain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                        <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
                        @error('formStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
