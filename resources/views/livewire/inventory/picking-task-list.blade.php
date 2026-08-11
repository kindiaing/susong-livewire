<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">拣货任务</h1>
            <p class="text-muted-foreground mt-1">管理拣货总单及状态流转</p>
        </div>
        @can('inventory.picking-task.create')
        <button type="button" wire:click="openGenerateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            生成拣货单
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索任务编号/线路/拣货员..." />
            @if($search)
                <button type="button" wire:click="$set('search','')" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部状态</option>
            <option value="1">待分配</option>
            <option value="2">拣货中</option>
            <option value="3">已完成</option>
        </select>
        <select wire:model.live="filterRouteId" class="flex h-9 w-40 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部线路</option>
            @foreach($routeOptions as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <input type="date" wire:model.live="filterDeliveryDate" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm" />
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('inventory.picking-task.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $statusMap = \App\Livewire\Inventory\PickingTaskList::$statusMap;
        $statusColorMap = \App\Livewire\Inventory\PickingTaskList::$statusColorMap;
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => !in_array($col['key'], ['task_no']))
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 160px';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 180px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>任务编号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-2 items-center hover:bg-muted/30 transition-colors"
                  style="grid-template-columns: {{ $gridCols }}"
                  wire:key="picking-task-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-mono font-medium text-foreground truncate">
                    <a href="{{ route('picking-tasks.detail', $item->id) }}" class="hover:text-blue-600 hover:underline transition-colors">{{ $item->task_no }}</a>
                </div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('route')
                            <div class="text-sm text-foreground">{{ $item->deliveryRoute?->name ?? '-' }}</div>
                            @break
                        @case('warehouse')
                            <div class="text-sm text-foreground">{{ $item->warehouse?->name ?? '-' }}</div>
                            @break
                        @case('picker')
                            <div class="text-sm text-foreground">{{ $item->picker?->name ?? '-' }}</div>
                            @break
                        @case('delivery_date')
                            <div class="text-sm text-foreground">{{ $item->delivery_date?->format('Y-m-d') ?? '-' }}</div>
                            @break
                        @case('status')
                            <div>
                                @php $c = $statusColorMap[$item->status] ?? 'gray'; @endphp
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ $statusMap[$item->status] ?? '-' }}</span>
                            </div>
                            @break
                        @case('total_skus')
                            <div class="text-sm text-foreground">{{ $item->total_skus }}</div>
                            @break
                        @case('total_quantity')
                            <div class="text-sm text-foreground">{{ $item->total_quantity }}</div>
                            @break
                        @case('started_at')
                            <div class="text-sm text-foreground">{{ $item->started_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            @break
                        @case('completed_at')
                            <div class="text-sm text-foreground">{{ $item->completed_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('inventory.picking-task.edit')
                    <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endcan
                    @can('inventory.picking-task.delete')
                    @if($item->status === 1)
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无拣货任务数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">编辑拣货任务</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">仓库</label>
                    <select wire:model="formWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择仓库</option>
                        @foreach($warehouseOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('formWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">拣货员</label>
                    <select wire:model="formPickerId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择拣货员</option>
                        @foreach($pickerOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('formPickerId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

    {{-- 生成拣货单弹窗 --}}
    @if($showGenerateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-4xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">生成拣货单</h2>
                <button type="button" wire:click="closeGenerateModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- 选择线路与日期 --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">配送线路 <span class="text-red-500">*</span></label>
                    <select wire:model.live="genRouteId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择线路</option>
                        @foreach($routeOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('genRouteId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">送达日期 <span class="text-red-500">*</span></label>
                    <input type="date" wire:model.live="genDeliveryDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('genDeliveryDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">仓库</label>
                    <input type="text" value="{{ $genWarehouseName ?: '-' }}" class="flex h-9 w-full rounded-md border border-input bg-muted px-3 text-sm text-muted-foreground" readonly />
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">拣货员</label>
                    <select wire:model="genPickerId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择拣货员</option>
                        @foreach($pickerOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('genPickerId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- 待拣货订单池 --}}
            @if(count($pendingOrders) > 0)
            <div class="border rounded-lg overflow-hidden mb-6">
                <div class="bg-muted/30 px-4 py-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-foreground">待拣货订单（{{ count($pendingOrders) }} 单）</span>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="selectAllGenOrders" class="text-xs text-blue-600 hover:text-blue-700 transition-colors">全选</button>
                        <span class="text-muted-foreground">|</span>
                        <button type="button" wire:click="deselectAllGenOrders" class="text-xs text-muted-foreground hover:text-foreground transition-colors">取消全选</button>
                    </div>
                </div>
                <div class="max-h-[40vh] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/20 sticky top-0">
                            <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                                <th class="w-10 px-3 py-2 text-left"><input type="checkbox" @if(count($genSelectedOrderIds) === count($pendingOrders)) checked @endif wire:click="{{ count($genSelectedOrderIds) === count($pendingOrders) ? 'deselectAllGenOrders' : 'selectAllGenOrders' }}" class="rounded" /></th>
                                <th class="px-3 py-2 text-left">订单编号</th>
                                <th class="px-3 py-2 text-left">商户</th>
                                <th class="px-3 py-2 text-left">商品摘要</th>
                                <th class="px-3 py-2 text-right">数量</th>
                                <th class="px-3 py-2 text-right">金额</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($pendingOrders as $idx => $order)
                            <tr class="hover:bg-muted/30 transition-colors {{ in_array($order['id'], $genSelectedOrderIds) ? 'bg-blue-50/50' : '' }}">
                                <td class="px-3 py-2"><input type="checkbox" value="{{ $order['id'] }}" @if(in_array($order['id'], $genSelectedOrderIds)) checked @endif wire:click="toggleGenOrder({{ $order['id'] }})" class="rounded" /></td>
                                <td class="px-3 py-2 font-mono text-xs text-foreground">{{ $order['order_no'] }}</td>
                                <td class="px-3 py-2 text-foreground">{{ $order['merchant_name'] }}</td>
                                <td class="px-3 py-2 text-muted-foreground truncate max-w-[200px]">{{ $order['product_summary'] ?: '-' }}</td>
                                <td class="px-3 py-2 text-right text-foreground">{{ $order['total_quantity'] }}</td>
                                <td class="px-3 py-2 text-right text-foreground">{{ money_format($order['total_amount']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @elseif($genRouteId > 0 && $genDeliveryDate)
            <div class="border rounded-lg p-8 text-center text-muted-foreground mb-6">
                <x-ui.icon name="inbox" class="w-8 h-8 mx-auto mb-2 text-muted-foreground/50" />
                <p class="text-sm">该线路在所选日期没有待拣货订单</p>
            </div>
            @else
            <div class="border rounded-lg p-8 text-center text-muted-foreground mb-6">
                <x-ui.icon name="inbox" class="w-8 h-8 mx-auto mb-2 text-muted-foreground/50" />
                <p class="text-sm">请先选择线路和送达日期</p>
            </div>
            @endif

            {{-- 底部按钮 --}}
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeGenerateModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="generatePickingTask" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors @if(empty($genSelectedOrderIds)) opacity-50 cursor-not-allowed @endif" @if(empty($genSelectedOrderIds)) disabled @endif>
                    确认生成（{{ count($genSelectedOrderIds) }} 单）
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.delete-confirm')
</div>