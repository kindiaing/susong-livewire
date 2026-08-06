<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">条码管理</h1>
            <p class="text-muted-foreground mt-1">管理SKU条码（厂家/供应商/内部/备用）</p>
        </div>
        @can('product.product.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增条码
        </button>
        @endcan
    </div>

    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索条码值/SKU编码..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterBarcodeType" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部类型</option>
            <option value="1">厂家条码</option>
            <option value="2">供应商条码</option>
            <option value="3">内部条码</option>
            <option value="4">备用条码</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('product.product.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded" /></th>
                    <th class="px-4 py-2 text-left w-16">ID</th>
                    <th class="px-4 py-2 text-left">SKU编码</th>
                    <th class="px-4 py-2 text-left">条码值</th>
                    <th class="px-4 py-2 text-left">条码类型</th>
                    <th class="px-4 py-2 text-left">供应商</th>
                    <th class="px-4 py-2 text-left">默认</th>
                    <th class="px-4 py-2 text-left">启用</th>
                    <th class="px-4 py-2 text-left">备注</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barcodes as $barcode)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="barcode-{{ $barcode->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $barcode->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $barcode->id }}</td>
                    <td class="px-4 py-2 font-medium text-foreground font-mono">{{ $barcode->sku?->sku_code ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground font-mono">{{ $barcode->barcode_code }}</td>
                    <td class="px-4 py-2 text-foreground">{{ \App\Models\SkuBarcode::barcodeTypeMap()[$barcode->barcode_type] ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $barcode->supplier?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $barcode->is_default ? '是' : '否' }}</td>
                    <td class="px-4 py-2">
                        {!! status_badge($barcode->is_enabled, 'active') !!}
                    </td>
                    <td class="px-4 py-2 text-muted-foreground truncate">{{ $barcode->remark ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('product.product.edit')
                            <button type="button" wire:click="openEditModal({{ $barcode->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endcan
                            @can('product.product.delete')
                            <button type="button" wire:click="confirmDelete({{ $barcode->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="px-6 py-12 text-center text-muted-foreground">暂无条码数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $barcodes->links() }}</div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑条码' : '新增条码' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.searchable-select label="SKU *" wire-model="formSkuId" :options="$skuOptions" placeholder="搜索SKU..." wireModel="formSkuId" />
                        @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">条码类型 <span class="text-red-500">*</span></label>
                        <select wire:model="formBarcodeType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">厂家条码</option>
                            <option value="2">供应商条码</option>
                            <option value="3">内部条码</option>
                            <option value="4">备用条码</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">条码值 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formBarcodeCode" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm font-mono" placeholder="请输入条码值" />
                    @error('formBarcodeCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.searchable-select label="供应商" wire-model="formSupplierId" :options="$supplierOptions" placeholder="搜索供应商..." wireModel="formSupplierId" clearable />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                        <input type="text" wire:model="formRemark" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">默认条码</label>
                        <select wire:model="formIsDefault" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">是否启用</label>
                        <select wire:model="formIsEnabled" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
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


    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
