@props([])

@php
// Toast system - Alpine.js store driven
@endphp

<div x-data="toastStore()"
     @toast:show.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none"
     x-cloak>

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             @click="removeToast(toast.id)"
             class="pointer-events-auto min-w-[320px] max-w-[420px] rounded-md border bg-background p-4 shadow-lg cursor-pointer"
             :class="{
                'border-blue-600/30 [&_strong]:text-blue-600': toast.variant === 'info',
                'border-green-600/30 [&_strong]:text-green-600': toast.variant === 'success',
                'border-orange-600/30 [&_strong]:text-orange-600': toast.variant === 'warning',
                'border-red-600/30 [&_strong]:text-red-600': toast.variant === 'destructive',
             }">

            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="shrink-0 mt-0.5">
                    <svg x-show="toast.variant === 'info'" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg x-show="toast.variant === 'success'" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg x-show="toast.variant === 'warning'" class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <svg x-show="toast.variant === 'destructive'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <strong class="text-sm font-semibold" x-text="toast.title"></strong>
                    <p class="text-sm text-muted-foreground mt-0.5" x-text="toast.description" x-show="toast.description"></p>
                </div>
                <!-- Close -->
                <button @click.stop="removeToast(toast.id)" class="shrink-0 rounded-sm p-1 hover:bg-accent transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('toastStore', () => ({
        toasts: [],
        nextId: 0,
        addToast({ title, description = '', variant = 'info', duration = 4000 }) {
            const id = this.nextId++;
            this.toasts.push({ id, title, description, variant, visible: true });
            if (duration > 0) {
                setTimeout(() => this.removeToast(id), duration);
            }
        },
        removeToast(id) {
            const idx = this.toasts.findIndex(t => t.id === id);
            if (idx > -1) {
                this.toasts[idx].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 200);
            }
        }
    }));
});
</script>
