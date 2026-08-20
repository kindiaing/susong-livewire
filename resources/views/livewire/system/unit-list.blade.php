<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">单位管理</h1>
            <p class="text-sm text-muted-foreground mt-1">单位主数据维护，如箱/件/包/斤/桶等</p>
        </div>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索单位名称..." />
            @if($search)
                <button type="button" wire:click="$set('search','')" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <div class="flex-1"></div>
        @if($selectedCount > 0)
        <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
        <button type="button" wire:click="clearSelection" class="text-xs text-muted-foreground hover:text-foreground">取消选择</button>
        @endif
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @can('system.system-config.view')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-1 rounded-md bg-primary px-3 py-1.5 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors"><x-ui.icon name="plus" class="w-4 h-4" />新增单位</button>
        @endcan
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
                @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="unit-list-{{ $item->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></td>
                    @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <td class="px-4 py-2 text-muted-foreground">{{ $item->id }}</td>
                            @break
                        @case('name')
                            <td class="px-4 py-2 text-foreground font-medium">{{ $item->name }}</td>
                            @break
                        @case('symbol')
                            <td class="px-4 py-2 text-muted-foreground">{{ $item->symbol ?? '-' }}</td>
                            @break
                        @case('sort')
                            <td class="px-4 py-2 text-foreground">{{ $item->sort }}</td>
                            @break
                        @case('status')
                            <td class="px-4 py-2">
                                <button type="button" wire:click="toggleStatus({{ $item->id }})" class="focus:outline-none">
                                    {!! status_badge($item->status, 'active') !!}
                                </button>
                            </td>
                            @break
                        @case('created_at')
                            <td class="px-4 py-2 text-muted-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                            @break
                        @default
                            <td class="px-4 py-2 text-foreground">{{ $item->{$col['key']} ?? '-' }}</td>
                    @endswitch
                    @endforeach
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('system.system-config.view')
                            <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil" class="w-3.5 h-3.5" /></button>
                            @endcan
                            @can('system.system-config.view')
                            <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $colspan }}" class="px-6 py-12 text-center text-muted-foreground">暂无数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeModal">
        <div class="bg-card rounded-lg shadow-xl w-full max-w-md mx-4" wire:click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑单位' : '新增单位' }}</h3>
                <button type="button" wire:click="closeModal" class="p-1 rounded hover:bg-muted transition-colors"><x-ui.icon name="x-mark" class="w-5 h-5 text-muted-foreground" /></button>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">单位名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如：箱、件、包" />
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">简称/符号</label>
                    <input type="text" wire:model="symbol" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如：X、J" />
                    @error('symbol') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">排序</label>
                    <input type="number" wire:model="sort" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">状态</label>
                    <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-1.5 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-primary px-4 py-1.5 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.delete-confirm')
    @include('partials.column-modal')
    @include('partials.export-modal')
</div>
