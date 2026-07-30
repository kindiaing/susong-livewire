{{-- 导入弹窗 --}}
@if($showImportModal)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" wire:click="closeImportModal"></div>
    <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
        <h2 class="text-lg font-semibold text-foreground mb-4">批量导入</h2>
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <button wire:click="downloadImportTemplate" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">
                    下载模板
                </button>
                <span class="text-xs text-muted-foreground">请先下载模板填写数据</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">选择文件</label>
                <input
                    type="file"
                    wire:model="importFile"
                    accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-muted-foreground
                        file:mr-4 file:py-1.5 file:px-3
                        file:rounded-md file:border-0
                        file:text-sm file:font-medium
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        file:cursor-pointer"
                />
                @error('importFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @if($importMessage)
                <div class="rounded-md bg-muted/50 px-3 py-2 text-sm text-foreground">{{ $importMessage }}</div>
            @endif
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button wire:click="closeImportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">关闭</button>
            <button wire:click="doImport" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">导入</button>
        </div>
    </div>
</div>
@endif
