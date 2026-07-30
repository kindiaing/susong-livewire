<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">采购单管理</h1>
            <p class="text-muted-foreground mt-1">采购单全流程：创建→接单→发货→入库→完成</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">新增采购单</button>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索单号/供应商..." />
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待接单</option><option value="2">备货中</option><option value="3">已发货</option>
            <option value="4">已入库</option><option value="5">完成</option><option value="9">取消</option>
        </select>
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_120px_1fr_100px_100px_80px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div><div>采购单号</div><div>供应商</div><div>总金额</div><div>实际金额</div><div>状态</div><div>操作</div>
        </div>
        @forelse($orders as $order)
            <div class="grid grid-cols-[60px_120px_1fr_100px_100px_80px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors" wire:key="po-{{ $order->id }}">
                <div class="text-sm text-muted-foreground">{{ $order->id }}</div>
                <div class="text-sm font-mono text-foreground">{{ $order->order_no }}</div>
                <div class="text-sm text-foreground">{{ $order->supplier?->name ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ $order->total_amount }}</div>
                <div class="text-sm text-foreground">{{ $order->actual_amount }}</div>
                <div>
                    @php $sc = ['1'=>'yellow','2'=>'blue','3'=>'orange','4'=>'green','5'=>'green','9'=>'gray']; $c = $sc[$order->status] ?? 'gray'; @endphp
                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ $order->status_label }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $order->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="confirmDelete({{ $order->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无采购单数据</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
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
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该采购单吗？</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
