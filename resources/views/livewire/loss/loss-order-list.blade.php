<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">损耗管理</h1>
            <p class="text-muted-foreground mt-1">管理损耗单据及审核流程</p>
        </div>
        @can('loss.loss-order.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增损耗单
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索损耗单号..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
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
            <option value="9">已作废</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导出</button>
            @if($selectedCount > 0)
                <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
                <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
                <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
            @endif
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    <th class="px-4 py-2 text-left">损耗单号</th>
                    <th class="px-4 py-2 text-left">仓库</th>
                    <th class="px-4 py-2 text-left">损耗类型</th>
                    <th class="px-4 py-2 text-left">损耗金额</th>
                    <th class="px-4 py-2 text-left">审核状态</th>
                    <th class="px-4 py-2 text-left">状态</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="loss-{{ $item->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 font-medium text-foreground">{{ $item->loss_no }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $item->warehouse?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ \App\Models\LossOrder::typeMap()[$item->loss_type] ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($item->total_amount) }}</td>
                    <td class="px-4 py-2">
                        {!! status_badge($item->approval_status, 'loss_approval') !!}
                    </td>
                    <td class="px-4 py-2">
                        {!! status_badge($item->status, 'loss_status') !!}
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('loss.loss-order.edit')
                            @if($item->approval_status === 1)
                                <button type="button" wire:click="confirmApprove({{ $item->id }})" class="text-green-600 hover:text-green-700 text-sm">审核</button>
                            @endif
                            @if($item->status === 2)
                                <button type="button" wire:click="execute({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">执行</button>
                            @endif
                            @if($item->status === 3)
                                <button type="button" wire:click="close({{ $item->id }})" class="text-orange-600 hover:text-orange-700 text-sm">关闭</button>
                            @endif
                            @if($item->status === 1)
                                <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil" class="w-3.5 h-3.5" /></button>
                            @endif
                            @endcan
                            @can('loss.loss-order.delete')
                            <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-muted-foreground">暂无损耗单数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
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
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
    @if($showApproveConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">审核损耗单</h2>
            <p class="text-sm text-muted-foreground mb-6">请选择审核结果</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeApproveConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="reject" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">拒绝</button>
                <button type="button" wire:click="approve" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">通过</button>
            </div>
        </div>
    </div>
    @endif
</div>
