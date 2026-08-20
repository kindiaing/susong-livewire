<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">复购模板</h1>
            <p class="text-muted-foreground mt-1">管理商家复购快速下单模板</p>
        </div>
        @can('order.cart.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增模板
        </button>
        @endcan
    </div>
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索模板名称..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <div class="flex-1"></div>
        @if($selectedCount > 0)
        <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
        @can('order.cart.delete')
        <button type="button" wire:click="batchDelete" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
        @endcan
        <button type="button" wire:click="clearSelection" class="text-xs text-muted-foreground hover:text-foreground">取消选择</button>
        @endif
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
    </div>
    @php
        $allCols = collect($this->getAllColumns());
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']))->values();
        $colspan = $visibleCols->count() + 2;
    @endphp
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></th>
                    @foreach($visibleCols as $col)
                    <th class="px-4 py-2 text-left">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $tpl)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="rt-{{ $tpl->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $tpl->id }}" wire:model.live="selectedIds" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></td>
                    @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <td class="px-4 py-2 text-muted-foreground">{{ $tpl->id }}</td>
                            @break
                        @case('name')
                            <td class="px-4 py-2 font-medium text-foreground">{{ $tpl->name }}</td>
                            @break
                        @case('merchant')
                            <td class="px-4 py-2 text-foreground">{{ $tpl->merchant?->name ?? '-' }}</td>
                            @break
                        @case('status')
                            <td class="px-4 py-2">{!! status_badge($tpl->status, 'active') !!}</td>
                            @break
                        @case('created_at')
                            <td class="px-4 py-2 text-muted-foreground">{{ $tpl->created_at?->format('Y-m-d H:i') }}</td>
                            @break
                        @default
                            <td class="px-4 py-2 text-foreground">{{ $tpl->{$col['key']} ?? '-' }}</td>
                    @endswitch
                    @endforeach
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('order.cart.edit')
                            <button type="button" wire:click="openEditModal({{ $tpl->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endcan
                            @can('order.cart.delete')
                            <button type="button" wire:click="confirmDelete({{ $tpl->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $colspan }}" class="px-6 py-12 text-center text-muted-foreground">暂无复购模板数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $templates->links() }}</div>
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑模板' : '新增模板' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <x-ui.searchable-select label="商家 *" wire-model="formMerchantId" :options="$merchantOptions" placeholder="搜索商家..." wireModel="formMerchantId" />
                    @error('formMerchantId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">模板名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入模板名称" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">状态</label>
                    <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">启用</option><option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
    @include('partials.delete-confirm')
    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
</div>
