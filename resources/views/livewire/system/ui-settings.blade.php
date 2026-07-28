<div x-data x-cloak>
    {{-- 界面设置 Drawer（底部窄面板） --}}
    <template x-teleport="body">
        <div x-show="$store.uiSettings.open" class="fixed inset-0 z-50">
            {{-- 遮罩 --}}
            <div class="fixed inset-0 bg-black/40"
                 x-show="$store.uiSettings.open"
                 x-transition:enter="transition-opacity ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in-out duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="$store.uiSettings.closeOnOutside ? $store.uiSettings.open = false : null"></div>

            {{-- 底部窄面板 --}}
            <div class="fixed inset-x-0 bottom-0 bg-background border-t border-border shadow-xl rounded-t-lg max-w-md mx-auto max-h-[85vh] flex flex-col"
                 x-show="$store.uiSettings.open"
                 x-transition:enter="transition-transform ease-in-out duration-300"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition-transform ease-in-out duration-200"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full">

                {{-- 拖拽指示条 --}}
                <div class="flex justify-center pt-3 pb-1">
                    <div class="w-10 h-1 rounded-full bg-border"></div>
                </div>

                {{-- 头部 --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-border">
                    <h3 class="text-sm font-semibold">界面设置</h3>
                    <button @click="$store.uiSettings.open = false" class="rounded-sm p-1 hover:bg-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- 设置内容 --}}
                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-6">

                    {{-- 通知行为设置组 --}}
                    <div>
                        <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">通知行为</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between rounded-sm border border-border p-3">
                                <div class="space-y-0.5 pr-3">
                                    <p class="text-sm font-medium">点击旁边关闭通知</p>
                                    <p class="text-xs text-muted-foreground">开启后，点击通知面板外的区域将自动关闭通知菜单</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center shrink-0">
                                    <input type="checkbox" wire:model.live="closeOnOutside" class="peer sr-only" />
                                    <div class="h-5 w-9 rounded-full bg-muted transition-colors peer-checked:bg-primary peer-focus-visible:outline-none peer-focus-visible:ring-2 peer-focus-visible:ring-ring peer-focus-visible:ring-offset-2 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:bg-background after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- 保存按钮 --}}
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center rounded-sm bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors disabled:opacity-50">
                        <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        保存设置
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
