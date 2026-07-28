@props(['value' => ''])
<div x-show="openMenu === '{{ $value }}'"
     x-cloak
     x-transition:enter="transition ease-out duration-100"
     x-transition:enter-start="opacity-0 -translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-75"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-1"
     class="absolute left-0 top-full pt-2"
     @click.away="openMenu = null">
    <div class="w-64 rounded-lg border bg-popover p-2 text-popover-foreground shadow-lg ring-1 ring-black/5">
        {{ $slot }}
    </div>
</div>
