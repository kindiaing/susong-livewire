@props([])
<nav {{ $attributes->merge(['class' => 'relative z-50']) }}
     x-data="{ openMenu: null }"
     @mouseleave="openMenu = null">
    <ul class="flex items-center gap-1">
        {{ $slot }}
    </ul>
</nav>
