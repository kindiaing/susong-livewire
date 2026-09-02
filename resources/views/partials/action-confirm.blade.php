{{-- 通用操作二次确认弹窗（作废/关闭/审核等，确认后调用 pendingAction） --}}
@if($showActionConfirm)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
    <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">{{ $actionConfirmTitle ?: '确认操作' }}</h2>
            <button type="button" wire:click="closeActionConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>
        @if($actionConfirmText)
        <p class="text-sm text-muted-foreground mb-6">{{ $actionConfirmText }}</p>
        @else
        <div class="mb-6"></div>
        @endif
        <div class="flex justify-end gap-3">
            <button type="button" wire:click="closeActionConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
            <button type="button" wire:click="executeActionConfirm" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">确认</button>
        </div>
    </div>
</div>
@endif
