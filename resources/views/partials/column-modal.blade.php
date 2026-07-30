{{-- 列配置弹窗 --}}
@if($showColumnModal)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" wire:click="closeColumnModal"></div>
    <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
        <h2 class="text-lg font-semibold text-foreground mb-4">自定义显示列</h2>
        <div class="space-y-1 max-h-64 overflow-y-auto border rounded-md p-3">
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
        <div class="flex justify-between items-center mt-4">
            <div class="flex gap-2">
                <button wire:click="selectAllColumns" class="text-xs text-blue-600 hover:text-blue-700">全选</button>
                <button wire:click="resetColumns" class="text-xs text-muted-foreground hover:text-foreground">恢复默认</button>
            </div>
            <button wire:click="closeColumnModal" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确定</button>
        </div>
    </div>
</div>
@endif
