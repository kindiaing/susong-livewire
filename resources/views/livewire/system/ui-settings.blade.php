<x-ui.slide-panel title="界面设置" width="380px" storeKey="uiSettings" :closeOnOutside="$closeOnOutside">
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
            <button type="button" wire:click="toggleCloseOnOutside"
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
</x-ui.slide-panel>
