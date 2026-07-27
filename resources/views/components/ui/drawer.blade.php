@props([
    'position' => 'right',
    'title' => '抽屉面板',
    'width' => '400px',
])

@php
$sideClasses = match($position) {
    'left' => 'left-0 inset-y-0',
    'right' => 'right-0 inset-y-0',
    'top' => 'top-0 inset-x-0',
    'bottom' => 'bottom-0 inset-x-0',
    default => 'right-0 inset-y-0',
};

$translateClasses = match($position) {
    'left' => '-translate-x-full',
    'right' => 'translate-x-full',
    'top' => '-translate-y-full',
    'bottom' => 'translate-y-full',
    default => 'translate-x-full',
};

$sizeStyle = match($position) {
    'left', 'right' => "width: {$width}",
    'top', 'bottom' => "height: {$width}",
    default => "width: {$width}",
};
@endphp

<div x-data="{ open: false }" {{ $attributes }}>
    <div @click="open = true">{{ $slot }}</div>

    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-50" x-cloak>
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black/40"
                 x-show="open"
                 x-transition:enter="transition-opacity ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in-out duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"></div>

            <!-- Drawer panel -->
            <div class="fixed {{ $sideClasses }} bg-background border-border shadow-xl"
                 style="{{ $sizeStyle }}"
                 x-show="open"
                 x-transition:enter="transition-transform ease-in-out duration-300"
                 x-transition:enter-start="{{ $translateClasses }}"
                 x-transition:enter-end="translate-x-0 translate-y-0"
                 x-transition:leave="transition-transform ease-in-out duration-200"
                 x-transition:leave-start="translate-x-0 translate-y-0"
                 x-transition:leave-end="{{ $translateClasses }}">

                <div class="flex items-center justify-between border-b border-border px-4 py-3">
                    <h3 class="text-sm font-semibold">{{ $title }}</h3>
                    <button @click="open = false" class="rounded-sm p-1 hover:bg-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="overflow-y-auto p-4" style="max-height: calc(100vh - 52px);">
                    {{ $slot->drawerContent ?? '' }}
                </div>
            </div>
        </div>
    </template>
</div>
