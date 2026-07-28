@props([
    'title' => '确认操作',
    'description' => '此操作无法撤销，请确认是否继续？',
    'confirmText' => '确认',
    'cancelText' => '取消',
    'variant' => 'destructive',
    'confirmAction' => '',
])

@php
$confirmClasses = match($variant) {
    'destructive' => 'bg-red-600 text-white hover:bg-red-600/90',
    'warning' => 'bg-orange-600 text-white hover:bg-orange-600/90',
    'info' => 'bg-blue-600 text-white hover:bg-blue-600/90',
    default => 'bg-foreground text-background hover:bg-foreground/90',
};
@endphp

<div x-data="{ open: false }" {{ $attributes }}>
    <div @click="open = true">{{ $slot }}</div>

    <template x-teleport="body">
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50"
             x-cloak>

            <!-- Overlay -->
            <div class="fixed inset-0 bg-black/50" @click="open = false"></div>

            <!-- Dialog -->
            <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-50 w-full max-w-lg"
                 x-show="open"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <div class="bg-background rounded-lg border border-border p-6 shadow-lg">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-muted-foreground">{{ $description }}</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="open = false"
                                class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground transition-colors">
                            {{ $cancelText }}
                        </button>
                        <button @click="open = false; {{ $confirmAction }}"
                                class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white transition-colors {{ $confirmClasses }}">
                            {{ $confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
