@props([
    'value' => null,
])

<div
    x-show="$data.activeTab === '{{ $value }}'"
    class="mt-2 focus-visible:outline-none"
    role="tabpanel"
>
    {{ $slot }}
</div>
