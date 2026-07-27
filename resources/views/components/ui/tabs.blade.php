@props([
    'defaultTab' => null,
])

<div
    x-data="{
        activeTab: '{{ $defaultTab ?? '' }}',
        switchTab(tab) { this.activeTab = tab; }
    }"
    {{ $attributes }}
>
    {{ $slot }}
</div>
