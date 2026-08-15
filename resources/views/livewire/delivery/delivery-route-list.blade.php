<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">配送线路管理</h1>
            <p class="text-muted-foreground mt-1">管理配送线路、商家排序与默认配置</p>
        </div>
        @can('delivery.route.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增线路
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索线路名称/编码..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-28 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">启用</option>
            <option value="0">停用</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('delivery.route.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'name')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 120px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAll" class="rounded" /></div>
            <div>线路名称</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($items as $route)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-2 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="route-{{ $route->id }}">
                <div><input type="checkbox" value="{{ $route->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="flex items-center gap-2">
                    @if($route->color)
                    <span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $route->color }}"></span>
                    @endif
                    <a href="{{ route('delivery-routes.detail', $route->id) }}" class="text-sm font-medium text-blue-600 hover:underline">{{ $route->name }}</a>
                </div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $route->id }}</div>
                            @break
                        @case('code')
                            <div class="text-sm font-mono text-foreground">{{ $route->code ?: '-' }}</div>
                            @break
                        @case('warehouse')
                            <div class="text-sm text-foreground">{{ $route->warehouse?->name ?? '-' }}</div>
                            @break
                        @case('default_driver')
                            <div class="text-sm text-foreground">{{ $route->defaultDriver?->name ?? '-' }}</div>
                            @break
                        @case('default_vehicle')
                            <div class="text-sm text-foreground">{{ $route->defaultVehicle?->plate_number ?? '-' }}</div>
                            @break
                        @case('color')
                            <div>
                                <span class="inline-block w-5 h-5 rounded border border-input" style="background-color: {{ $route->color ?: '#ccc' }}"></span>
                            </div>
                            @break
                        @case('departure_time')
                            <div class="text-sm text-foreground">{{ $route->departure_time ? $route->departure_time->format('H:i') : '-' }}</div>
                            @break
                        @case('stops_count')
                            <div class="text-sm text-foreground">{{ $route->stops_count }}</div>
                            @break
                        @case('status')
                            <div>
                                {!! status_badge($route->status, 'active') !!}
                            </div>
                            @break
                        @case('sort')
                            <div class="text-sm text-foreground">{{ $route->sort }}</div>
                            @break
                        @case('remark')
                            <div class="text-sm text-foreground truncate max-w-[150px]">{{ $route->remark ?: '-' }}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $route->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $route->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('delivery.route.edit')
                    <button type="button" wire:click="openEditModal({{ $route->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endcan
                    @can('delivery.route.delete')
                    <button type="button" wire:click="confirmDelete({{ $route->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无线路数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-2xl mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑配送线路' : '新增配送线路' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">线路名称 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入线路名称" />
                        @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">线路编码</label>
                        <input type="text" wire:model="formCode" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 R01" />
                        @error('formCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">出发仓库</label>
                        <select wire:model="formWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择仓库</option>
                            @foreach($warehouseOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('formWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">出发时间</label>
                        <input type="time" wire:model="formDepartureTime" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('formDepartureTime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">默认司机</label>
                        <select wire:model="formDefaultDriverId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择司机</option>
                            @foreach($driverOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('formDefaultDriverId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">默认车辆</label>
                        <select wire:model="formDefaultVehicleId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择车辆</option>
                            @foreach($vehicleOptions as $id => $plate)
                            <option value="{{ $id }}">{{ $plate }}</option>
                            @endforeach
                        </select>
                        @error('formDefaultVehicleId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">线路颜色</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model="formColor" class="w-9 h-9 rounded border border-input p-1 cursor-pointer" />
                            <span class="text-xs text-muted-foreground">{{ $formColor }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">预计时长（分钟）</label>
                        <input type="number" wire:model="formEstimatedDuration" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" placeholder="分钟" />
                        @error('formEstimatedDuration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">预计里程（公里）</label>
                        <input type="number" step="0.01" wire:model="formEstimatedDistance" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" placeholder="公里" />
                        @error('formEstimatedDistance') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">描述</label>
                    <textarea wire:model="formDescription" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formDescription') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">排序</label>
                        <input type="number" wire:model="formSort" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                        <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="0">停用</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
