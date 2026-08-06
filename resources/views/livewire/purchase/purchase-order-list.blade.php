<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">采购单管理</h1>
            <p class="text-muted-foreground mt-1">采购单全流程：创建→接单→发货→入库→完成</p>
        </div>
        @can('purchase.purchase-order.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增采购单
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索单号/供应商..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待接单</option>
            <option value="2">备货中</option>
            <option value="3">已发货</option>
            <option value="4">已入库</option>
            <option value="5">完成</option>
            <option value="9">已作废</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
            @if($selectedCount > 0)
                <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
                <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
                <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
            @endif
    </div>

    {{-- 采购单列表 --}}
    @php
        $visibleCols = collect($this->getAllColumns())->filter(fn($col) => $this->isColumnVisible($col['key']));
    @endphp
    <div class="rounded-lg border bg-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2.5 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    @foreach($visibleCols as $col)
                        <th class="px-4 py-2.5 text-left">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-2.5 text-right w-24">操作</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="po-{{ $order->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    @foreach($visibleCols as $col)
                        @switch($col['key'])
                            @case('order_no')
                                <td class="px-4 py-2">
                                    <a href="{{ route('purchase-orders.detail', $order->id) }}" class="font-mono text-blue-600 hover:text-blue-700">{{ $order->order_no }}</a>
                                </td>
                                @break
                            @case('supplier_id')
                                <td class="px-4 py-2">{{ $order->supplier?->name ?? '-' }}</td>
                                @break
                            @case('purchase_date')
                                <td class="px-4 py-2 text-foreground">{{ $order->purchase_date?->format('Y-m-d') ?? '-' }}</td>
                                @break
                            @case('status')
                                <td class="px-4 py-2">{!! status_badge($order->status, 'purchase_order') !!}</td>
                                @break
                            @case('total_amount')
                                <td class="px-4 py-2 text-right">{{ money_format($order->total_amount) }}</td>
                                @break
                            @case('actual_amount')
                                <td class="px-4 py-2 text-right">{{ money_format($order->actual_amount) }}</td>
                                @break
                            @case('created_at')
                                <td class="px-4 py-2 text-muted-foreground">{{ $order->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                @break
                            @default
                                <td class="px-4 py-2 text-foreground">{{ $order->{$col['key']} ?? '-' }}</td>
                        @endswitch
                    @endforeach
                    <td class="px-4 py-2 text-right">
                        <div class="inline-flex items-center gap-0.5">
                            {{-- 详情 --}}
                            @can('purchase.purchase-order.view')
                            <a href="{{ route('purchase-orders.detail', $order->id) }}" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="详情"><x-ui.icon name="eye" class="w-3.5 h-3.5" /></a>
                            @endcan
                            {{-- 编辑 --}}
                            @can('purchase.purchase-order.edit')
                            <button type="button" wire:click="openEditModal({{ $order->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endcan
                            {{-- 删除（未入库状态可删除） --}}
                            @can('purchase.purchase-order.delete')
                            @if(in_array($order->status, [1, 2, 3]))
                                <button type="button" wire:click="confirmDelete({{ $order->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ $visibleCols->count() + 2 }}" class="px-4 py-10 text-center text-muted-foreground">暂无采购单数据</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑采购单' : '新增采购单' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">供应商 <span class="text-red-500">*</span></label>
                    <select wire:model="formSupplierId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择供应商</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                    @error('formSupplierId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">采购日期</label>
                    <input type="date" wire:model="formPurchaseDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
