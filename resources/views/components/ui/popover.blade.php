@props([
    'side' => 'bottom',
    'align' => 'center',
    'offset' => 8,
    'trigger' => 'click',
])

@php
$sideClasses = match($side) {
    'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-' . $offset,
    'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-' . $offset,
    'left' => 'right-full top-1/2 -translate-y-1/2 mr-' . $offset,
    'right' => 'left-full top-1/2 -translate-y-1/2 ml-' . $offset,
};

$alignClasses = match($align) {
    'start' => $side === 'top' || $side === 'bottom' ? 'left-0 -translate-x-0' : 'top-0 -translate-y-0',
    'center' => '',
    'end' => $side === 'top' || $side === 'bottom' ? 'right-0 -translate-x-0' : 'bottom-0 -translate-y-0',
};

// center 需要保留 -translate-x-1/2 或 -translate-y-1/2
if ($align !== 'center') {
    $positionClasses = $sideClasses;
    // 替换对齐相关
    if ($side === 'top' || $side === 'bottom') {
        $positionClasses = str_replace('left-1/2 -translate-x-1/2', $alignClasses ?: 'left-1/2 -translate-x-1/2', $positionClasses);
    } else {
        $positionClasses = str_replace('top-1/2 -translate-y-1/2', $alignClasses ?: 'top-1/2 -translate-y-1/2', $positionClasses);
    }
} else {
    $positionClasses = $sideClasses;
}

$triggerEvent = $trigger === 'hover' ? '@mouseenter="open = true" @mouseleave="open = false"' : '@click="open = !open"';
$contentHover = $trigger === 'hover' ? '@mouseenter="open = true" @mouseleave="open = false"' : '';
@endphp

<div
    x-data="{ open: false }"
    @click.away="open = false"
    @keydown.escape.window="if(open) open = false"
    class="relative inline-block"
    {{ $attributes }}
>
    {{-- Trigger --}}
    <div {{ $triggerEvent }}>
        {{ $slot->trigger ?? '' }}
    </div>

    {{-- Popover Content --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @if($contentHover) {{ $contentHover }} @endif
        class="absolute z-50 min-w-[8rem] w-max rounded-md border border-border bg-popover p-4 text-popover-foreground shadow-md outline-none {{ $positionClasses }}"
        role="dialog"
    >
        {{ $slot->content ?? '' }}
    </div>
</div>
