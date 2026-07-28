@props(['value' => ''])
<li class="relative" x-data="{ menuKey: '{{ $value }}', hoverTimer: null }"
    @mouseenter="hoverTimer = setTimeout(() => { openMenu = menuKey }, 50)"
    @mouseleave="clearTimeout(hoverTimer); openMenu = null">
    {{ $slot }}
</li>
