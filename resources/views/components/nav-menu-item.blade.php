@props(['value' => ''])
<li class="relative" x-data="{ menuKey: '{{ $value }}' }"
    @mouseenter="openMenu = menuKey"
    @mouseleave="openMenu = null">
    {{ $slot }}
</li>
