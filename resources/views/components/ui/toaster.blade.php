@props([])
{{--
  Toast 全局容器 — 放在 app-layout 底部
  Alpine.js store 在 app.js 中注册，此组件仅负责渲染
  通过 window.$toast.success(title, desc) 或 Livewire dispatch('toast', {...}) 触发
--}}
<div x-data
     class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2 w-full max-w-sm"
     style="pointer-events: none;">
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             @click="Alpine.store('toasts').remove(toast.id)"
             class="pointer-events-auto group flex items-start gap-3 rounded-lg border bg-background p-4 shadow-lg cursor-default"
             :class="{
                'border-border': toast.type === 'default',
                'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950': toast.type === 'success',
                'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950': toast.type === 'error',
                'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950': toast.type === 'warning',
                'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950': toast.type === 'info',
             }">

            {{-- 状态图标 --}}
            <div class="shrink-0 mt-0.5 w-5 h-5">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.062l.053.053a.75.75 0 0 1-.053 1.07l-.053.053a.75.75 0 0 1-1.07-.053l-.02-.041-.02-.042a.75.75 0 0 1 .032-.775ZM12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm0-9v3m0 3h.008v.008H12V12Z"/></svg>
                </template>
            </div>

            {{-- 内容 --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold leading-none"
                   :class="{
                      'text-foreground': toast.type === 'default',
                      'text-green-800 dark:text-green-200': toast.type === 'success',
                      'text-red-800 dark:text-red-200': toast.type === 'error',
                      'text-yellow-800 dark:text-yellow-200': toast.type === 'warning',
                      'text-blue-800 dark:text-blue-200': toast.type === 'info',
                   }"
                   x-text="toast.title"></p>
                <p x-show="toast.description"
                   class="mt-1 text-sm leading-snug opacity-90"
                   :class="{
                      'text-muted-foreground': toast.type === 'default',
                      'text-green-700 dark:text-green-300': toast.type === 'success',
                      'text-red-700 dark:text-red-300': toast.type === 'error',
                      'text-yellow-700 dark:text-yellow-300': toast.type === 'warning',
                      'text-blue-700 dark:text-blue-300': toast.type === 'info',
                   }"
                   x-text="toast.description"></p>
            </div>

            {{-- 关闭按钮 --}}
            <button @click.stop="Alpine.store('toasts').remove(toast.id)"
                    class="shrink-0 rounded-md p-0.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/5 dark:hover:bg-white/10">
                <svg class="w-4 h-4 text-current opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
