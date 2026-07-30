{{-- 列表页通用工具栏：搜索 + 重置 + 列配置 + 导入 + 导出 + 批量操作 --}}
<div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
    {{-- 左侧：搜索 + 重置 --}}
    <div class="flex items-center gap-3">
        <input
            type="text"
            wire:model.live="search"
            class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="{{ $searchPlaceholder ?? '搜索...' }}"
        />
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 右侧：功能按钮 --}}
    <div class="flex items-center gap-2">
        {{-- 已选信息 --}}
        @if($selectedCount ?? 0 > 0)
            <span class="text-xs text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            <button wire:click="clearSelection" class="text-xs text-red-500 hover:text-red-600 transition-colors">清除</button>
            <div class="h-4 w-px bg-border mx-1"></div>
        @endif

        {{-- 批量操作 --}}
        @if($selectedCount ?? 0 > 0)
            @foreach($batchActions ?? [] as $action)
                <button wire:click="{{ $action['method'] }}" class="inline-flex items-center gap-1 rounded-md {{ $action['color'] ?? 'bg-orange-600 hover:bg-orange-700' }} px-3 py-1.5 text-xs font-medium text-white transition-colors">
                    {{ $action['label'] }}
                </button>
            @endforeach
            <div class="h-4 w-px bg-border mx-1"></div>
        @endif

        {{-- 列配置 --}}
        <button wire:click="$toggle('showColumnModal')" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors" title="自定义显示列">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            列
        </button>

        {{-- 导入 --}}
        @if($showImport ?? false)
            <button wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors" title="批量导入">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                导入
            </button>
        @endif

        {{-- 导出 --}}
        <button wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors" title="导出Excel">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            导出
        </button>
    </div>
</div>
