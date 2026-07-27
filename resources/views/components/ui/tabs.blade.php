@props([
    'defaultTab' => null,
])

<div
    x-data="{
        activeTab: '{{ $defaultTab }}' || '{{ collect($slot)->first()?parameter('tab') ?? '' }}',
        switchTab(tab) { this.activeTab = tab; }
    }"
    {{ $attributes->merge(['class' => '']) }}
>
    {{ $slot }}
</div>
