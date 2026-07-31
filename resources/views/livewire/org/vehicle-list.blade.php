<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">车辆管理</h1>
            <p class="text-muted-foreground mt-1">管理车辆信息及冷链标记</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增车辆
        </button>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input
            type="text"
            wire:model.live="search"
            class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="搜索车牌号/车辆类型..."
        />
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
        <div class="flex-1"></div>
        <button wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导入</button>
        <button wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            <button wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            <button wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 车辆列表 --}}
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[40px_60px_120px_1fr_80px_80px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div><input type="checkbox" wire:model.live="selectAll" class="rounded" /></div>
            <div>ID</div>
            <div>车牌号</div>
            <div>车辆类型</div>
            <div>冷链</div>
            <div>状态</div>
            <div>操作</div>
        </div>

        @forelse($vehicles as $vehicle)
            <div class="grid grid-cols-[40px_60px_120px_1fr_80px_80px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="vehicle-{{ $vehicle->id }}">
                <div><input type="checkbox" value="{{ $vehicle->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm text-muted-foreground">{{ $vehicle->id }}</div>
                <div class="text-sm font-medium text-foreground font-mono">{{ $vehicle->plate_number }}</div>
                <div class="text-sm text-foreground">{{ $vehicle->vehicle_type ?? '-' }}</div>
                <div>
                    @if($vehicle->is_cold_chain === 1)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-blue-100 text-blue-700">冷链</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">非冷链</span>
                    @endif
                </div>
                <div>
                    @if($vehicle->status === 1)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">启用</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">禁用</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $vehicle->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="confirmDelete({{ $vehicle->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
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
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑车辆' : '新增车辆' }}</h2>
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
                <button wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该车辆吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
