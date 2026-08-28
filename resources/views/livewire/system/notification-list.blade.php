<div>
    {{-- 标题栏 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">通知管理</h1>
            <p class="text-sm text-muted-foreground mt-1">发送系统通知，支持全站广播和指定用户推送</p>
        </div>
        @can('system.notification.create')
        <button type="button" wire:click="openCreateModal"
                class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            发送通知
        </button>
        @endcan
    </div>

    {{-- 筛选栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索标题或内容..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="typeFilter" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部类型</option>
            @foreach($typeMap as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex-1"></div>
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">标题</th>
                    <th class="px-4 py-2 text-left">类型</th>
                    <th class="px-4 py-2 text-left">发送范围</th>
                    <th class="px-4 py-2 text-left">内容预览</th>
                    <th class="px-4 py-2 text-left">创建时间</th>
                    <th class="px-4 py-2 text-left w-16">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="notif-{{ $item->id }}">
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->id }}</td>
                    <td class="px-4 py-2 text-foreground font-medium">{{ $item->title }}</td>
                    <td class="px-4 py-2">
                        @php
                            $color = $colorMap[$item->type] ?? 'blue';
                            $label = $typeMap[$item->type] ?? '未知';
                        @endphp
                        <span class="inline-flex items-center rounded-full bg-{{ $color }}-100 px-2 py-0.5 text-xs font-medium text-{{ $color }}-700">{{ $label }}</span>
                    </td>
                    <td class="px-4 py-2">
                        @if($item->user_id === null)
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs text-blue-600">全站广播</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                                指定用户
                                @if($item->user)
                                    ({{ $item->user->name ?? $item->user->username ?? '#' . $item->user_id }})
                                @endif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-muted-foreground truncate max-w-xs">{{ $item->content ?? '-' }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('system.notification.create')
                            <button type="button" wire:click="confirmDelete({{ $item->id }})"
                                    class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除">
                                <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        {{-- 遮罩（不可点击关闭） --}}
        <div class="fixed inset-0 bg-black/40"></div>

        {{-- 弹窗主体 --}}
        <div class="relative w-full max-w-2xl rounded-lg bg-background shadow-xl border border-border">
            {{-- 标题栏 --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="text-base font-semibold">发送通知</h3>
                <button type="button" wire:click="closeModal" class="rounded-sm p-1 hover:bg-accent transition-colors">
                    <x-ui.icon name="x-mark" class="w-4 h-4" />
                </button>
            </div>

            {{-- 表单 --}}
            <form wire:submit="save" class="px-6 py-4 space-y-4">
                {{-- 标题 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">标题 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formTitle"
                           class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm"
                           placeholder="请输入通知标题" />
                    @error('formTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 正文 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">正文 <span class="text-red-500">*</span></label>
                    <textarea wire:model="formContent" rows="4"
                              class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm resize-none"
                              placeholder="请输入通知内容"></textarea>
                    @error('formContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 类型 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">类型</label>
                    <select wire:model="formType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @foreach($typeMap as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 发送范围 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">发送范围 <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="formTarget" value="all" class="h-4 w-4 text-blue-600 border-input" />
                            <span class="text-sm">全站广播</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="formTarget" value="user" class="h-4 w-4 text-blue-600 border-input" />
                            <span class="text-sm">指定用户</span>
                        </label>
                    </div>
                    @error('formTarget') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 指定用户（条件显示） --}}
                @if($formTarget === 'user')
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">目标用户 <span class="text-red-500">*</span></label>
                    <select wire:model="formUserId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">请选择用户</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name ?? $user->username }}（{{ $user->username ?? 'ID:' . $user->id }}）</option>
                        @endforeach
                    </select>
                    @error('formUserId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- 按钮区 --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                    <button type="button" wire:click="closeModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                    <button type="submit"
                            class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
                        发送
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="fixed inset-0 bg-black/40" wire:click="closeDeleteConfirm"></div>
        <div class="relative w-full max-w-sm rounded-lg bg-background shadow-xl border border-border p-6">
            <p class="text-sm text-foreground mb-4">确认删除此通知？删除后用户将不再看到该通知。</p>
            <div class="flex items-center justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm"
                        class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
