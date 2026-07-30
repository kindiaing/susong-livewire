<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">采购退货</h1>
            <p class="text-muted-foreground mt-1">管理采购退货单据</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增退货单
        </button>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索退货单号..." />
        <select wire:model.live="filterStatus" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待审核</option>
            <option value="2">已审核</option>
            <option value="3">已出库</option>
            <option value="4">完成</option>
            <option value="9">取消</option>
        </select>
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <div class="grid grid-cols-[60px_1fr_1fr_1fr_120px_120px_80px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider min-w-[900px]">
            <div>ID</div>
            <div>退货单号</div>
            <div>供应商</div>
            <div>仓库</div>
            <div>退货金额</div>
            <div>实际金额</div>
            <div>状态</div>
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid grid-cols-[60px_1fr_1fr_1fr_120px_120px_80px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors min-w-[900px]"
                 wire:key="preturn-{{ $item->id }}">
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm font-medium text-foreground">{{ $item->return_no }}</div>
                <div class="text-sm text-foreground">{{ $item->supplier?->name ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ $item->warehouse?->name ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ number_format($item->total_amount / 100, 2) }} 元</div>
                <div class="text-sm text-foreground">{{ number_format($item->actual_amount / 100, 2) }} 元</div>
                <div>
                    @php($statusLabel = \App\Models\PurchaseReturn::statusMap()[$item->status] ?? '未知')
                    @if($item->status === 1)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-yellow-100 text-yellow-700">{{ $statusLabel }}</span>
                    @elseif($item->status === 4)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">{{ $statusLabel }}</span>
                    @elseif($item->status === 9)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">{{ $statusLabel }}</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-blue-100 text-blue-700">{{ $statusLabel }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无退货单数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑退货单' : '新增退货单' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">关联采购单 <span class="text-red-500">*</span></label>
                    <select wire:model="formPurchaseOrderId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择采购单</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}">{{ $po->order_no }}</option>
                        @endforeach
                    </select>
                    @error('formPurchaseOrderId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">供应商 <span class="text-red-500">*</span></label>
                        <select wire:model="formSupplierId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('formSupplierId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">出库仓库 <span class="text-red-500">*</span></label>
                        <select wire:model="formWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                        @error('formWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">退货原因 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formReason" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入退货原因" />
                    @error('formReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
            <p class="text-sm text-muted-foreground mb-6">确定要删除该退货单吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
