{{-- 导出弹窗 --}}
@if($showExportModal)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
    <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">导出 Excel</h2>
            <button type="button" wire:click="closeExportModal" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>
        <div class="space-y-4">
            {{-- 格式选择 --}}
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">文件格式</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="exportFormat" value="xlsx" class="text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm text-foreground">XLSX</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="exportFormat" value="csv" class="text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm text-foreground">CSV</span>
                    </label>
                </div>
            </div>
            {{-- 列选择 --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-foreground">导出列</label>
                    <button type="button" wire:click="exportSelectAllColumns" class="text-xs text-blue-600 hover:text-blue-700">全选</button>
                </div>
                <div class="space-y-1 max-h-48 overflow-y-auto border rounded-md p-3">
                    @foreach($this->getExportableColumns() as $col)
                        <label class="flex items-center gap-2 px-2 py-1 rounded hover:bg-muted/30 cursor-pointer transition-colors">
                            <input
                                type="checkbox"
                                @if(in_array($col['key'], $exportColumns)) checked @endif
                                wire:click="toggleExportColumn('{{ $col['key'] }}')"
                                class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-sm text-foreground">{{ $col['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-muted-foreground mt-1">已选 {{ count($exportColumns) }} 列</p>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" wire:click="closeExportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
            <button type="button" wire:click="doExport" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">导出</button>
        </div>
    </div>
</div>
@endif
