@props([
    'value' => null,
])

<button
    type="button"
    @click="$data.activeTab = '{{ $value }}'"
    :class="$data.activeTab === '{{ $value }}' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'"
    class="inline-flex items-center justify-center whitespace-nowrap rounded-sm border-b-2 px-3 py-1.5 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
    role="tab"
    :aria-selected="$data.activeTab === '{{ $value }}'"
>
    {{ $slot }}
</button>
