@props([
    'align' => 'start',
    'side' => 'bottom',
    'offset' => 4,
])

@php
$alignClasses = match($align) {
    'start' => 'left-0 origin-top-left',
    'center' => 'left-1/2 -translate-x-1/2 origin-top',
    'end' => 'right-0 origin-top-right',
};

$sideClasses = match($side) {
    'bottom' => 'top-full mt-' . $offset,
    'top' => 'bottom-full mb-' . $offset,
};
@endphp

<div
    x-data="{ open: false }"
    @click.away="open = false"
    @keydown.escape.window="if(open) open = false"
    class="relative inline-block"
    {{ $attributes }}
>
    {{-- Trigger --}}
    <div @click="open = !open">
        {!! $slot->trigger ?? '' !!}
    </div>

    {{-- Dropdown Content --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 min-w-[8rem] overflow-hidden rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md {{ $alignClasses }} {{ $sideClasses }}"
    >
        {!! $slot->content ?? '' !!}
    </div>
</div>
