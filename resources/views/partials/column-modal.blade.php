{{-- 列配置弹窗 --}}
@if($showColumnModal)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
    <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">自定义显示列</h2>
            <button type="button" wire:click="closeColumnModal" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>
        <div class="space-y-1 max-h-96 overflow-y-auto border rounded-md p-3">
            @foreach($allColumns ?? [] as $col)
                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-muted/30 cursor-pointer transition-colors">
                    <input
                        type="checkbox"
                        @if(in_array($col['key'], $visibleColumns)) checked @endif
                        wire:click="toggleColumn('{{ $col['key'] }}')"
                        class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500"
                    />
                    <span class="text-sm text-foreground">{{ $col['label'] }}</span>
                </label>
            @endforeach
        </div>
        <div class="flex justify-between items-center mt-6">
            <div class="flex gap-2">
                <button type="button" wire:click="selectAllColumns" class="text-xs text-blue-600 hover:text-blue-700">全选</button>
                <button type="button" wire:click="resetColumns" class="text-xs text-muted-foreground hover:text-foreground">恢复默认</button>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeColumnModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">关闭</button>
                <button type="button" wire:click="closeColumnModal" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确定</button>
            </div>
        </div>
    </div>
</div>
@endif
