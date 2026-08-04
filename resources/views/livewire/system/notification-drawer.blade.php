@php
    $notifications = $this->notifications;
@endphp

{{-- 通知 Drawer — 内联 slide-panel 避免 Livewire 4 与 Blade 组件命名 slot 不兼容 --}}
<div
    x-data="{ notificationPanelOpen: false }"
    @keydown.escape.window="if(notificationPanelOpen) notificationPanelOpen = false"
>
    {{-- 触发器：通知铃铛 --}}
    <button type="button"
            class="relative p-2 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors"
            title="通知"
            @click="notificationPanelOpen = true">
        <x-ui.icon name="bell" class="w-5 h-5" />
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-destructive text-[10px] font-medium text-destructive-foreground">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- 右侧滑出面板 --}}
    <template x-teleport="body">
        <div x-show="notificationPanelOpen" class="fixed inset-0 z-50" x-cloak>
            {{-- 遮罩 --}}
            <div class="fixed inset-0 bg-black/40"
                 x-show="notificationPanelOpen"
                 x-transition:enter="transition-opacity ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in-out duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="notificationPanelOpen = false"></div>

            {{-- 面板 --}}
            <div class="fixed right-0 inset-y-0 bg-background border-l border-border shadow-xl flex flex-col"
                 style="width: 400px;"
                 x-show="notificationPanelOpen"
                 x-transition:enter="transition-transform ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition-transform ease-in-out duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                {{-- 标题栏 --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-border shrink-0">
                    <h3 class="text-sm font-semibold">通知</h3>
                    <button type="button" @click="notificationPanelOpen = false" class="rounded-sm p-1 hover:bg-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- 头部补充信息（未读角标 + 全部已读） --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-border">
                    @if($unreadCount > 0)
                        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1.5 text-[10px] font-medium text-primary-foreground">
                            {{ $unreadCount }} 条未读
                        </span>
                        <button type="button" wire:click="markAllRead"
                                class="text-xs text-muted-foreground hover:text-foreground px-2 py-1 rounded hover:bg-accent transition-colors">
                            全部已读
                        </button>
                    @else
                        <span></span>
                    @endif
                </div>

                {{-- 筛选 Tab --}}
                <div class="flex border-b border-border px-4">
                    @foreach(['全部', '未读'] as $tab)
                        <button type="button"
                                wire:click="setTab('{{ $tab }}')"
                                @class([
                                    'px-3 py-2 text-sm font-medium border-b-2 transition-colors -mb-px',
                                    'border-primary text-foreground' => $activeTab === $tab,
                                    'border-transparent text-muted-foreground hover:text-foreground' => $activeTab !== $tab,
                                ])>
                            {{ $tab }}
                        </button>
                    @endforeach
                </div>

                {{-- 通知列表 --}}
                <div class="overflow-y-auto flex-1" style="max-height: calc(100vh - 110px);">
                    @forelse($notifications as $n)
                        <div class="px-4 py-3 hover:bg-accent/50 transition-colors cursor-pointer border-b border-border/50 last:border-0 {{ $n->is_read === 0 ? 'bg-primary/5' : '' }}"
                             wire:click="markRead({{ $n->id }})">
                            <div class="flex gap-3">
                                {{-- 类型图标 --}}
                                <div class="shrink-0 mt-0.5">
                                    @if($n->type === App\Models\Notification::TYPE_SYSTEM)
                                        <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-blue-100 text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.443A2.5 2.5 0 0118 14.11V9a6.002 6.002 0 00-4-5.659V3a2 2 0 10-4 0v.341C7.67 4.165 6 6.388 6 9v5.11c0 .822-.334 1.6-.915 2.196L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        </div>
                                    @elseif($n->type === App\Models\Notification::TYPE_ORDER)
                                        <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-green-100 text-green-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                    @elseif($n->type === App\Models\Notification::TYPE_RESTOCK)
                                        <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-orange-100 text-orange-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </div>
                                    @elseif($n->type === App\Models\Notification::TYPE_INVENTORY)
                                        <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-red-100 text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        </div>
                                    @elseif($n->type === App\Models\Notification::TYPE_ACCOUNT)
                                        <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-purple-100 text-purple-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- 内容 --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-sm font-medium truncate">{{ $n->title }}</p>
                                        @if($n->is_read === 0)
                                            <span class="shrink-0 mt-1 h-2 w-2 rounded-full bg-primary"></span>
                                        @endif
                                    </div>
                                    @if($n->content)
                                        <p class="text-xs text-muted-foreground mt-0.5 line-clamp-2">{{ $n->content }}</p>
                                    @endif
                                    <p class="text-[11px] text-muted-foreground/70 mt-1">{{ $n->time_ago }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-muted-foreground">
                            <svg class="w-10 h-10 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.443A2.5 2.5 0 0118 14.11V9a6.002 6.002 0 00-4-5.659V3a2 2 0 10-4 0v.341C7.67 4.165 6 6.388 6 9v5.11c0 .822-.334 1.6-.915 2.196L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <p class="text-sm">暂无通知</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </template>
</div>
