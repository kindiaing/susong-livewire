<div>
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">促销活动</h1>
            <p class="text-muted-foreground mt-1">管理促销活动、满减、优惠券等营销规则</p>
        </div>
        @can('price.promotion.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增活动
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索活动名称..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
            @if($selectedCount > 0)
                <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
                <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
                <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
            @endif
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2.5 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    @foreach($allColumns as $col)
                        @if($col['key'] !== 'id')
                            <th class="px-4 py-2.5 text-left">{{ $col['label'] }}</th>
                        @endif
                    @endforeach
                    <th class="px-4 py-2.5 text-right w-20">操作</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="promo-{{ $item->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 font-medium text-foreground">{{ $item->name }}</td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-blue-50 text-blue-700">{{ $typeMap[$item->promo_type] ?? '未知' }}</span>
                    </td>
                    <td class="px-4 py-2">
                        <button type="button" wire:click="toggleStatus({{ $item->id }})" title="{{ $item->status === 1 ? '点击禁用' : '点击启用' }}" class="inline-flex items-center justify-center">
                            @if($item->status === 1)
                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-green-500 hover:bg-green-600 transition-colors">
                                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 13.5-13.5"/></svg>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors">
                                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            @endif
                        </button>
                    </td>
                    <td class="px-4 py-2 text-muted-foreground text-xs">{{ $item->start_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2 text-muted-foreground text-xs">{{ $item->end_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2 text-muted-foreground text-xs">{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2 text-right">
                        <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-muted-foreground">暂无促销活动数据</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 创建弹窗 --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">新增促销活动</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">活动名称</label>
                    <input type="text" wire:model="createFormName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('createFormName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">活动类型</label>
                    <select wire:model.live="createFormType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @foreach($typeMap as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('createFormType') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">开始时间</label>
                        <input type="datetime-local" wire:model="createFormStartAt" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('createFormStartAt') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">结束时间</label>
                        <input type="datetime-local" wire:model="createFormEndAt" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('createFormEndAt') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeCreateModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认创建</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @include('partials.delete-confirm')

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
</div>
