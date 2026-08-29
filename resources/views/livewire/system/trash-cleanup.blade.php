<div class="">
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">数据清理</h1>
        <p class="text-muted-foreground mt-1">统计并永久清理已软删除的业务单据，仅超级管理员可操作</p>
    </div>

    {{-- 总览卡片 --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-lg border bg-card p-4">
            <div class="flex items-center gap-2 text-muted-foreground text-sm">
                <x-ui.icon name="trash" class="w-4 h-4" />
                <span>待清理类型</span>
            </div>
            <p class="text-2xl font-bold text-foreground mt-2">{{ count(array_filter($trashTypes, fn($t) => $t['count'] > 0)) }}</p>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <div class="flex items-center gap-2 text-muted-foreground text-sm">
                <x-ui.icon name="document-text" class="w-4 h-4" />
                <span>已删除记录总数</span>
            </div>
            <p class="text-2xl font-bold text-foreground mt-2">{{ $totalTrashed }}</p>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <div class="flex items-center gap-2 text-muted-foreground text-sm">
                <x-ui.icon name="circle-check" class="w-4 h-4" />
                <span>可清理类型</span>
            </div>
            <p class="text-2xl font-bold text-foreground mt-2">{{ count($trashTypes) }}</p>
        </div>
    </div>

    {{-- 提示信息 --}}
    <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-200 p-3 text-sm text-yellow-800">
        <div class="flex items-start gap-2">
            <x-ui.icon name="exclamation-triangle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <div>
                <p class="font-medium">永久清理操作不可恢复</p>
                <p class="mt-1 text-xs">点击「永久清理」按钮将删除该类型所有已软删除的记录（含子表数据），请谨慎操作。</p>
            </div>
        </div>
    </div>

    {{-- 清理列表 --}}
    <div class="rounded-lg border bg-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-muted/30">
                    <th class="px-4 py-2 text-left font-medium text-muted-foreground">单据类型</th>
                    <th class="px-4 py-2 text-right font-medium text-muted-foreground">已删除数量</th>
                    <th class="px-4 py-2 text-right font-medium text-muted-foreground">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashTypes as $key => $type)
                <tr>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="{{ $type['icon'] ?? 'document' }}" class="w-4 h-4 text-muted-foreground" />
                            <span class="font-medium text-foreground">{{ $type['label'] }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-right">
                        @if($type['count'] > 0)
                        <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700">{{ $type['count'] }}</span>
                        @else
                        <span class="text-xs text-muted-foreground">0</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right">
                        @if($type['count'] > 0)
                        <button type="button" wire:click="openConfirm('{{ $key }}')" class="rounded-md bg-red-600 px-3 py-1 text-xs font-medium text-white hover:bg-red-700 transition-colors">
                            永久清理
                        </button>
                        @else
                        <span class="text-xs text-muted-foreground">无待清理数据</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 确认弹窗 --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeConfirm" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground text-red-600">永久清理确认</h2>
                <button type="button" wire:click="closeConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="rounded-md bg-red-50 border border-red-200 p-3 text-sm text-red-800">
                    <p>您即将永久清理以下数据：</p>
                    <ul class="mt-2 space-y-1">
                        <li>单据类型：<strong>{{ $confirmLabel }}</strong></li>
                        <li>已删除记录：<strong>{{ $confirmCount }} 条</strong></li>
                    </ul>
                    <p class="mt-2 text-xs">此操作不可恢复，删除后数据将无法找回。</p>
                </div>
                <p class="text-sm text-muted-foreground">请确认是否继续？</p>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeCleanup" wire:loading.attr="disabled" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                    <span wire:loading.remove>确认永久清理</span>
                    <span wire:loading>清理中...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
