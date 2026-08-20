<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">历史通知</h1>
            <p class="text-muted-foreground mt-1">查看全部通知记录，包括已读、未读和已隐藏的通知</p>
        </div>
        @if($this->unreadCount > 0)
            <button type="button" wire:click="markAllRead"
                    class="inline-flex items-center gap-1.5 rounded-md border border-border bg-background px-3 py-1.5 text-sm font-medium text-foreground hover:bg-accent transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                全部标记已读
            </button>
        @endif
    </div>

    {{-- Tab 筛选 --}}
    <div class="flex border-b border-border mb-4">
        @foreach(['全部', '未读', '已读', '已隐藏'] as $tab)
            <button type="button"
                    wire:click="setTab('{{ $tab }}')"
                    @class([
                        'px-4 py-2 text-sm font-medium border-b-2 transition-colors -mb-px',
                        'border-primary text-foreground' => $activeTab === $tab,
                        'border-transparent text-muted-foreground hover:text-foreground' => $activeTab !== $tab,
                    ])>
                {{ $tab }}
                @if($tab === '未读' && $this->unreadCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center rounded-full bg-primary text-primary-foreground text-[10px] px-1.5 h-4 leading-none">
                        {{ $this->unreadCount }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- 通知列表 --}}
    <div class="overflow-hidden rounded-lg border border-border">
        @forelse($notifications as $n)
            <div class="flex gap-4 px-4 py-3 border-b border-border/50 last:border-0 {{ $n->is_read === 0 ? 'bg-primary/5' : '' }} {{ $n->hidden === 1 ? 'opacity-60' : '' }}">
                {{-- 类型图标 --}}
                <div class="shrink-0 mt-0.5">
                    @if($n->type === App\Models\Notification::TYPE_SYSTEM)
                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-blue-100 text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.443A2.5 2.5 0 0118 14.11V9a6.002 6.002 0 00-4-5.659V3a2 2 0 10-4 0v.341C7.67 4.165 6 6.388 6 9v5.11c0 .822-.334 1.6-.915 2.196L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                    @elseif($n->type === App\Models\Notification::TYPE_ORDER)
                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-green-100 text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    @elseif($n->type === App\Models\Notification::TYPE_RESTOCK)
                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-orange-100 text-orange-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                    @elseif($n->type === App\Models\Notification::TYPE_INVENTORY)
                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-red-100 text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                    @elseif($n->type === App\Models\Notification::TYPE_ACCOUNT)
                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-purple-100 text-purple-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- 内容 --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium {{ $n->is_read === 0 ? '' : 'text-muted-foreground' }}">{{ $n->title }}</p>
                        @if($n->is_read === 0)
                            <span class="shrink-0 h-2 w-2 rounded-full bg-primary"></span>
                        @endif
                        @if($n->hidden === 1)
                            <span class="shrink-0 inline-flex items-center rounded bg-muted px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground">已隐藏</span>
                        @endif
                        <span class="shrink-0 inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium text-{{ $n->type_color }}-600 bg-{{ $n->type_color }}-100">{{ $n->type_label }}</span>
                    </div>
                    @if($n->content)
                        <p class="text-sm text-muted-foreground mt-1">{{ $n->content }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-[11px] text-muted-foreground/70">{{ $n->time_ago }}</span>
                        <span class="text-[11px] text-muted-foreground/70">{{ $n->created_at?->format('Y-m-d H:i') }}</span>
                    </div>
                </div>

                {{-- 操作按钮 --}}
                <div class="flex items-center gap-1 shrink-0">
                    @if($n->is_read === 0)
                        <button type="button" wire:click="markRead({{ $n->id }})" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            标记已读
                        </button>
                    @else
                        <button type="button" wire:click="markUnread({{ $n->id }})"
                                class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            标记未读
                        </button>
                    @endif

                    @if($n->hidden === 1)
                        <button type="button" wire:click="restoreHidden({{ $n->id }})"
                                class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            恢复显示
                        </button>
                    @endif

                    <button type="button" wire:click="deleteNotification({{ $n->id }})" wire:confirm="确定删除这条通知吗？此操作不可恢复。"
                            class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        删除
                    </button>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-muted-foreground">
                <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.443A2.5 2.5 0 0118 14.11V9a6.002 6.002 0 00-4-5.659V3a2 2 0 10-4 0v.341C7.67 4.165 6 6.388 6 9v5.11c0 .822-.334 1.6-.915 2.196L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <p class="text-sm">暂无通知记录</p>
            </div>
        @endforelse
    </div>

    {{-- 分页 --}}
    {{ $notifications->links() }}
</div>
