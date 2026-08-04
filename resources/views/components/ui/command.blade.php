@props([])

@php
// 从 config/menu.php 生成命令面板数据（单一数据源，与顶部导航保持一致）
$commandGroups = collect(config('menu', []))->map(function ($module) {
    return [
        'label' => $module['label'],
        'items' => collect($module['children'] ?? [])->map(function ($item) {
            $url = '#';
            try { $url = route($item['route']); } catch (\Throwable $e) {}
            return [
                'label' => $item['label'],
                'url'   => $url,
            ];
        })->values()->toArray(),
    ];
})->values()->toArray();
@endphp

{{-- 注入菜单数据到 window，供 Alpine JS 组件使用 --}}
<script>window.__commandGroups = @json($commandGroups);</script>

<div x-data="commandPalette()"
     @keydown.window="if(($event.metaKey || $event.ctrlKey) && $event.key === 'k') { $event.preventDefault(); open = !open }"
     @keydown.window.escape="if(open) open = false">

    {{-- 触发按钮 --}}
    <button type="button" @click="open = true"
            class="flex items-center gap-2 h-8 px-3 rounded-md border border-input bg-background text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors cursor-pointer"
            title="搜索 (Ctrl+K)">
        <x-ui.icon name="magnifying-glass" class="w-4 h-4" />
        <span class="hidden sm:inline">搜索...</span>
        <kbd class="hidden sm:inline-flex items-center gap-0.5 h-5 px-1.5 ml-2 rounded border border-border bg-muted text-[10px] font-medium text-muted-foreground">
            <span>⌘</span>K
        </kbd>
    </button>

    {{-- 弹窗 --}}
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-50" x-cloak>
            {{-- 遮罩 --}}
            <div class="fixed inset-0 bg-black/40"
                 x-show="open"
                 x-transition:enter="transition-opacity ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"></div>

            {{-- 弹窗主体 --}}
            <div class="fixed inset-x-0 top-[15%] mx-auto max-w-xl px-4"
                 x-show="open"
                 x-transition:enter="ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <div class="overflow-hidden rounded-lg border bg-popover text-popover-foreground shadow-xl">
                    {{-- 搜索输入框 --}}
                    <div class="flex items-center border-b px-3">
                        <x-ui.icon name="magnifying-glass" class="w-4 h-4 shrink-0 text-muted-foreground" />
                        <input type="text"
                               x-ref="searchInput"
                               x-model="query"
                               @keydown.down.prevent="navigateDown()"
                               @keydown.up.prevent="navigateUp()"
                               @keydown.enter.prevent="selectItem()"
                               placeholder="搜索菜单、功能、页面..."
                               class="flex-1 h-12 bg-transparent px-3 text-sm outline-none placeholder:text-muted-foreground" />
                        <kbd class="flex items-center h-5 px-1.5 rounded border border-border bg-muted text-[10px] font-medium text-muted-foreground cursor-pointer"
                             @click="open = false">ESC</kbd>
                    </div>

                    {{-- 搜索结果 --}}
                    <div class="max-h-80 overflow-y-auto py-2">
                        <template x-if="filteredGroups.length === 0">
                            <div class="py-6 text-center text-sm text-muted-foreground">
                                没有找到匹配的结果
                            </div>
                        </template>

                        <template x-for="group in filteredGroups" :key="group.label">
                            <div class="px-2 pt-2 first:pt-0">
                                <p class="px-2 mb-1 text-xs font-medium text-muted-foreground" x-text="group.label"></p>
                                <template x-for="(item, idx) in group.items" :key="item.label">
                                    <button type="button"
                                            @click="goTo(item)"
                                            @mouseenter="activeIndex = group.label + '-' + idx"
                                            :class="activeIndex === group.label + '-' + idx ? 'bg-accent text-accent-foreground' : 'text-popover-foreground'"
                                            class="flex w-full items-center gap-3 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors cursor-pointer hover:bg-accent hover:text-accent-foreground">
                                        <span class="shrink-0 w-4 h-4 text-muted-foreground">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                        </span>
                                        <span class="flex-1 truncate" x-text="item.label"></span>
                                        <span x-show="item.shortcut" class="text-xs text-muted-foreground" x-text="item.shortcut"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- 底部提示 --}}
                    <div class="flex items-center justify-between border-t px-3 py-2 text-xs text-muted-foreground">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1"><kbd class="rounded border border-border bg-muted px-1 py-0.5 text-[10px]">↑↓</kbd> 导航</span>
                            <span class="flex items-center gap-1"><kbd class="rounded border border-border bg-muted px-1 py-0.5 text-[10px]">↵</kbd> 选择</span>
                        </div>
                        <span>Command Palette</span>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
