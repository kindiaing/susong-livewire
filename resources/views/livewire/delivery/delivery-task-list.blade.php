<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">配送任务</h1>
            <p class="text-muted-foreground mt-1">管理配送任务及状态流转</p>
        </div>
        @can('delivery.delivery-task.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增任务
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索订单号/司机..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待配送</option>
            <option value="2">配送中</option>
            <option value="3">任务完成</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('delivery.delivery-task.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $statusMap = [1 => '待配送', 2 => '配送中', 3 => '任务完成'];
        $statusColorMap = [1 => 'yellow', 2 => 'orange', 3 => 'green'];
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'order_no')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 180px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>订单号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="delivery-task-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-mono font-medium text-foreground truncate">{{ $item->order?->order_no ?? '-' }}</div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('driver')
                            <div class="text-sm text-foreground">{{ $item->driver?->name ?? '-' }}</div>
                            @break
                        @case('vehicle')
                            <div class="text-sm text-foreground">{{ $item->vehicle?->plate_number ?? '-' }}</div>
                            @break
                        @case('status')
                            <div>
                                @php $c = $statusColorMap[$item->status] ?? 'gray'; @endphp
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ $statusMap[$item->status] ?? '-' }}</span>
                            </div>
                            @break
                        @case('delivery_date')
                            <div class="text-sm text-foreground">{{ $item->planned_at?->format('Y-m-d') ?? '-' }}</div>
                            @break
                        @case('started_at')
                            <div class="text-sm text-foreground">{{ $item->started_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            @break
                        @case('completed_at')
                            <div class="text-sm text-foreground">{{ $item->completed_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            @break
                        @case('note')
                            <div class="text-sm text-foreground truncate max-w-[200px]">{{ $item->note ?: '-' }}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('delivery.delivery-task.edit')
                    <button type="button" wire:click="openEditModal({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    @endcan
                    @can('delivery.delivery-task.create')
                    @if($item->status === 1)
                    <button type="button" wire:click="startDelivery({{ $item->id }})" class="text-green-600 hover:text-green-700 text-sm">开始配送</button>
                    @endif
                    @endcan
                    @can('delivery.delivery-task.create')
                    @if($item->status === 2)
                    <button type="button" wire:click="completeDelivery({{ $item->id }})" class="text-green-600 hover:text-green-700 text-sm">完成</button>
                    @endif
                    @endcan
                    @can('delivery.delivery-task.delete')
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无配送任务数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑配送任务' : '新增配送任务' }}</h2>
            <div class="space-y-4">
                @if(!$editingId)
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">订单 <span class="text-red-500">*</span></label>
                    <select wire:model="formOrderId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择订单</option>
                        @foreach($orderOptions as $id => $orderNo)
                        <option value="{{ $id }}">{{ $orderNo }}</option>
                        @endforeach
                    </select>
                    @error('formOrderId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">司机 <span class="text-red-500">*</span></label>
                    <select wire:model="formDriverId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择司机</option>
                        @foreach($driverOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('formDriverId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
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
                    <label class="block text-sm font-medium text-foreground mb-1">配送日期 <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="formDeliveryDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formDeliveryDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formNote" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formNote') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
