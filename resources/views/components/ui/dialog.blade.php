@props([
    'open' => false,
    'size' => 'default',
])

@php
$sizeClasses = match($size) {
    'sm' => 'max-w-sm',
    'default' => 'max-w-lg',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
    'full' => 'max-w-[calc(100vw-2rem)]',
};
@endphp

<div
    x-data="{ open: {{ $open ? 'true' : 'false' }} }"
    x-init="
        window.addEventListener('open-dialog', (e) => { if (e.detail.id === $id('dialog')) open = true; });
        window.addEventListener('close-dialog', (e) => { if (e.detail.id === $id('dialog')) open = false; });
    "
    @keydown.escape.window="open = false"
>
    {{-- Trigger --}}
    <div @click="open = true">
        {{ $slot->trigger ?? '' }}
    </div>

    {{-- Portal --}}
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50"
        >
            {{-- Backdrop --}}
            <div
                class="fixed inset-0 bg-black/80"
                @click="open = false"
                aria-hidden="true"
            ></div>

            {{-- Content --}}
            <div
                x-show="open"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full {{ $sizeClasses }} z-50"
                role="dialog"
                aria-modal="true"
            >
                <div class="bg-background rounded-lg border border-border shadow-lg focus-visible:outline-none">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
