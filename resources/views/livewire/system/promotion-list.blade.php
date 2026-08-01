<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">运营主推</h1>
        </div>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索标题..." />
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
        <div class="flex-1"></div>
        @if($selectedCount > 0)
        <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
        @can('system.banner.delete')
        <button wire:click="batchDelete" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
        @endcan
        <button wire:click="clearSelection" class="text-xs text-muted-foreground hover:text-foreground">取消选择</button>
        @endif
        <button wire:click="openColumnModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">列配置</button>
        <button wire:click="openExportModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">导出</button>
        <button wire:click="openImportModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">导入</button>
    </div>
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[40px_60px_1fr_1fr_80px_80px_120px_100px] gap-2 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></div>
            <div>ID</div><div>商品</div><div>标题</div><div>排序</div><div>状态</div><div>创建时间</div><div>操作</div>
        </div>
        @forelse($items as $item)
            <div class="grid grid-cols-[40px_60px_1fr_1fr_80px_80px_120px_100px] gap-2 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors" wire:key="promotion-list-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></div>
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm text-foreground">{{ $item->product?->name ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ $item->title }}</div>
                <div class="text-sm text-foreground">{{ $item->sort }}</div>
                <div>
                    @if($item->status === 1)<span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">启用</span>
                    @else<span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">禁用</span>@endif
                </div>
                <div class="text-sm text-muted-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
                <div class="flex items-center gap-2">
                    @can('system.banner.delete')
                    <button wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无数据</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该记录吗？</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
</div>
