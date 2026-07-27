@props([
    'side' => 'top',
    'offset' => 4,
    'delay' => 200,
])

@php
$sideClasses = match($side) {
    'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-' . $offset,
    'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-' . $offset,
    'left' => 'right-full top-1/2 -translate-y-1/2 mr-' . $offset,
    'right' => 'left-full top-1/2 -translate-y-1/2 ml-' . $offset,
};

$arrowClasses = match($side) {
    'top' => 'top-full left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-b-transparent border-t-popover',
    'bottom' => 'bottom-full left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-t-transparent border-b-popover',
    'left' => 'left-full top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-r-transparent border-l-popover',
    'right' => 'right-full top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-l-transparent border-r-popover',
};
@endphp

<div
    x-data="{ visible: false, timeout: null }"
    @mouseenter="timeout = setTimeout(() => visible = true, {{ $delay }})"
    @mouseleave="clearTimeout(timeout); visible = false"
    class="relative inline-block"
    {{ $attributes }}
>
    {{-- Trigger --}}
    <div>
        {{ $slot }}
    </div>

    {{-- Tooltip Content --}}
    <div
        x-show="visible"
        x-transition:enter="ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 {{ $sideClasses }}"
        role="tooltip"
    >
        <div class="rounded-md bg-popover px-3 py-1.5 text-xs text-popover-foreground border border-border shadow-md whitespace-nowrap">
            {{ $slot->tooltip ?? '' }}
        </div>
    </div>
</div>
