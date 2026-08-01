<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">损耗管理</h1>
            <p class="text-muted-foreground mt-1">管理损耗单据及审核流程</p>
        </div>
        @can('loss.loss-order.create')
        <button type="button" wire:click="l" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增损耗单
        </button>
        @endcan
            新增损耗单
        </button>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索损耗单号..." />
        <select wire:model.live="filterLossType" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部类型</option>
            <option value="1">存储腐坏</option>
            <option value="2">称重失水</option>
            <option value="3">过期报废</option>
            <option value="4">加工损耗</option>
            <option value="5">盘点差异</option>
            <option value="6">其他</option>
        </select>
        <select wire:model.live="filterStatus" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待审核</option>
            <option value="2">已通过</option>
            <option value="3">已执行</option>
            <option value="4">已关闭</option>
            <option value="9">已取消</option>
        </select>
        <button type="button" wire:click="s" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <div class="grid grid-cols-[60px_1fr_1fr_100px_120px_80px_80px_120px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider min-w-[900px]">
            <div>ID</div>
            <div>损耗单号</div>
            <div>仓库</div>
            <div>损耗类型</div>
            <div>损耗金额</div>
            <div>审核状态</div>
            <div>状态</div>
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid grid-cols-[60px_1fr_1fr_100px_120px_80px_80px_120px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors min-w-[900px]"
                 wire:key="loss-{{ $item->id }}">
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm font-medium text-foreground">{{ $item->loss_no }}</div>
                <div class="text-sm text-foreground">{{ $item->warehouse?->name ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ \App\Models\LossOrder::typeMap()[$item->loss_type] ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ number_format($item->total_amount / 100, 2) }} 元</div>
                <div>
                    @php($asLabel = \App\Models\LossOrder::approvalStatusMap()[$item->approval_status] ?? '未知')
                    @if($item->approval_status === 2)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">{{ $asLabel }}</span>
                    @elseif($item->approval_status === 3)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-red-100 text-red-700">{{ $asLabel }}</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-yellow-100 text-yellow-700">{{ $asLabel }}</span>
                    @endif
                </div>
                <div>
                    @php($sLabel = \App\Models\LossOrder::statusMap()[$item->status] ?? '未知')
                    @if(in_array($item->status, [3, 4]))
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-blue-100 text-blue-700">{{ $sLabel }}</span>
                    @elseif($item->status === 9)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">{{ $sLabel }}</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-yellow-100 text-yellow-700">{{ $sLabel }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @can('loss.loss-order.edit')
                    @if($item->approval_status === 1)
                        <button type="button" wire:click=")" class="text-green-600 hover:text-green-700 text-sm">审核</button>
                    @endif
                    @if($item->status === 2)
                        <button type="button" wire:click=")" class="text-blue-600 hover:text-blue-700 text-sm">执行</button>
                    @endif
                    @if($item->status === 3)
                        <button type="button" wire:click=")" class="text-orange-600 hover:text-orange-700 text-sm">关闭</button>
                    @endif
                    @if($item->status === 1)
                        <button type="button" wire:click=")" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    @endif
                    @endcan
                    @can('loss.loss-order.delete')
                    <button type="button" wire:click=")" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无损耗单数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑损耗单' : '新增损耗单' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">仓库 <span class="text-red-500">*</span></label>
                    <select wire:model="formWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择仓库</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                    @error('formWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">损耗类型 <span class="text-red-500">*</span></label>
                    <select wire:model="formLossType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">存储腐坏</option>
                        <option value="2">称重失水</option>
                        <option value="3">过期报废</option>
                        <option value="4">加工损耗</option>
                        <option value="5">盘点差异</option>
                        <option value="6">其他</option>
                    </select>
                    @error('formLossType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">损耗原因</label>
                    <input type="text" wire:model="formReason" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                    @error('formReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="e" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
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
            <p class="text-sm text-muted-foreground mb-6">确定要删除该损耗单吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="e" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 审核弹窗 --}}
    @if($showApproveConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeApproveConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">审核损耗单</h2>
            <p class="text-sm text-muted-foreground mb-6">请选择审核结果</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeApproveConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="t" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">拒绝</button>
                <button type="button" wire:click="e" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">通过</button>
            </div>
        </div>
    </div>
    @endif
</div>
