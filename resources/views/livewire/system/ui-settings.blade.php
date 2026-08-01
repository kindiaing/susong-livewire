<div x-data x-cloak>
    {{-- 界面设置 Drawer（右侧全屏，同通知面板效果） --}}
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

            {{-- 右侧抽屉面板 --}}
            <div class="fixed right-0 inset-y-0 bg-background border-l border-border shadow-xl flex flex-col"
                 style="width: 380px;"
                 x-show="$store.uiSettings.open"
                 x-transition:enter="transition-transform ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition-transform ease-in-out duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                {{-- 头部 --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-border shrink-0">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="cog-6-tooth" class="w-5 h-5 text-muted-foreground" />
                        <h3 class="text-sm font-semibold">界面设置</h3>
                    </div>
                    <button type="button" @click="$store.uiSettings.open = false" class="rounded-sm p-1 hover:bg-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- 设置列表 --}}
                <div class="flex-1 overflow-y-auto">

                    {{-- 通知设置 --}}
                    <div class="px-5 pt-5 pb-2">
                        <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">通知</h4>
                    </div>

                    <div class="px-5 py-3 border-b border-border hover:bg-muted/30 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-foreground">点击旁边关闭通知</p>
                                <p class="text-xs text-muted-foreground mt-0.5">开启后，点击通知面板外的区域将自动关闭通知菜单</p>
                            </div>
                            <button type="button"
                                wire:click="toggleCloseOnOutside"
                                role="switch"
                                :aria-checked="$wire.closeOnOutside"
                                class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 {{ $closeOnOutside ? 'bg-primary' : 'bg-muted' }}"
                            >
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-background shadow ring-0 transition duration-200 ease-in-out {{ $closeOnOutside ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    </div>

                    {{-- 个人中心 --}}
                    <div class="px-5 pt-5 pb-2">
                        <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">个人中心</h4>
                    </div>

                    <div class="px-5 py-3 border-b border-border">
                        <p class="text-xs text-muted-foreground">暂无可配置项</p>
                    </div>

                </div>
            </div>
        </div>
    </template>
</div>
