{{-- 删除确认弹窗 --}}
@if($showDeleteConfirm)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
    <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">确认删除</h2>
            <button type="button" wire:click="closeDeleteConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>
        <p class="text-sm text-muted-foreground mb-6">{{ $deleteWarning ?? $deleteConfirmMessage ?? '确定要删除吗？此操作不可恢复。' }}</p>
        <div class="flex justify-end gap-3">
            <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
            @if(!isset($canDelete) || $canDelete)
            <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            @endif
        </div>
    </div>
</div>
@endif
