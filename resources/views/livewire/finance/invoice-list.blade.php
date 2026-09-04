<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">发票管理</h1>
            <p class="text-muted-foreground mt-1">管理发票开具及寄出</p>
        </div>
        @can('finance.invoice.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增发票
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索发票号/商家..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterType" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部类型</option>
            @foreach(\App\Models\Invoice::typeMap() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            @foreach(\App\Models\Invoice::statusMap() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex items-center gap-1" title="按创建日期筛选">
            <input type="date" wire:model.live="filterDateStart" class="flex h-9 rounded-md border border-input bg-background px-2 text-sm" />
            <span class="text-muted-foreground text-sm">~</span>
            <input type="date" wire:model.live="filterDateEnd" class="flex h-9 rounded-md border border-input bg-background px-2 text-sm" />
        </div>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('finance.invoice.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $statusColorMap = [1 => 'yellow', 2 => 'green', 3 => 'blue'];
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'invoice_no')
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
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>发票号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div class="action-col">操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="invoice-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-mono font-medium text-foreground truncate"><a href="{{ route('invoices.detail', $item->id) }}" class="text-blue-600 hover:underline">{{ $item->invoice_no }}</a></div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('merchant')
                            <div class="text-sm text-foreground truncate">{{ $item->merchant?->name ?? '-' }}</div>
                            @break
                        @case('amount')
                            <div class="text-sm text-foreground">{{ money_format($item->amount) }}</div>
                            @break
                        @case('type')
                            <div class="text-sm text-foreground">{{ \App\Livewire\Finance\InvoiceList::$typeMap[$item->type] ?? '-' }}</div>
                            @break
                        @case('title')
                            <div class="text-sm text-foreground truncate">{{ $item->title ?? '-' }}</div>
                            @break
                        @case('tax_no')
                            <div class="text-sm text-foreground">{{ $item->tax_no ?? '-' }}</div>
                            @break
                        @case('status')
                            <div>
                                @php $c = $statusColorMap[$item->status] ?? 'gray'; @endphp
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ \App\Livewire\Finance\InvoiceList::$statusMap[$item->status] ?? '-' }}</span>
                            </div>
                            @break
                        @case('issued_at')
                            <div class="text-sm text-foreground">{{ $item->issued_at?->format('Y-m-d H:i') ?? '-' }}</div>
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
                <div class="action-col flex items-center gap-0.5">
                    @can('finance.invoice.edit')
                    @if($item->canEdit())
                    <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('finance.invoice.create')
                    @if($item->status === 1)
                    <button type="button" wire:click="issue({{ $item->id }})" class="p-1 rounded text-green-600 hover:bg-green-50 hover:text-green-700 transition-colors" title="开具"><x-ui.icon name="document-text" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('finance.invoice.create')
                    @if($item->status === 2)
                    <button type="button" wire:click="send({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="寄出"><x-ui.icon name="truck" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('finance.invoice.delete')
                    @if($item->canEdit())
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无发票数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑发票' : '新增发票' }}
                    @if($editingStatusLabel)
                    <span class="ml-2 inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-{{ $editingStatusColor }}-100 text-{{ $editingStatusColor }}-700">{{ $editingStatusLabel }}</span>
                    @endif
                </h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <x-ui.searchable-select label="商家 *" wire:model="formMerchantId" placeholder="搜索商家..." wireModel="formMerchantId" :value="$formMerchantId" :selected-label="$selectedMerchant?->name ?? ''" search="searchMerchantOptions" />
                    @error('formMerchantId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">金额（元） <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formAmount" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入金额（元）" />
                    @error('formAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">类型 <span class="text-red-500">*</span></label>
                    <select wire:model="formType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">增值税普通发票</option>
                        <option value="1">增值税专用发票</option>
                    </select>
                    @error('formType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">抬头 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formTitle" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入发票抬头" />
                    @error('formTitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">税号 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formTaxNo" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入税号" />
                    @error('formTaxNo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">发票文件</label>
                    @if($existingFileUrl)
                    <div class="mb-2">
                        <a href="{{ asset('storage/' . $existingFileUrl) }}" target="_blank" class="text-blue-600 hover:underline text-sm">查看当前文件</a>
                    </div>
                    @endif
                    <input type="file" wire:model="formFileUrl" class="flex w-full text-sm" accept=".pdf,.jpg,.jpeg,.png" />
                    @error('formFileUrl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-muted-foreground mt-1">支持 PDF/JPG/PNG，最大 10MB{{ $editingId ? '，上传新文件将替换旧文件' : '' }}</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingId ? '更新' : '保存' }}</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
