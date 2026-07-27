@props(['align' => 'center', 'sideOffset' => 4])

<div x-data="{ show: false, timer: null }"
     @mouseenter="timer = setTimeout(() => show = true, 100)"
     @mouseleave="clearTimeout(timer); show = false"
     class="relative inline-block"
     {{ $attributes }}>

    <div class="cursor-pointer">{{ $slot }}</div>

    <template x-teleport="body">
        <div x-show="show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             x-cloak
             class="fixed z-50 w-64 rounded-md border border-border bg-popover p-4 text-popover-foreground shadow-md outline-none"
             @mouseenter="show = true; clearTimeout(timer)"
             @mouseleave="show = false">

            {{ $slot->hoverContent ?? '' }}
        </div>
    </template>
</div>
