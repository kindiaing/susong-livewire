@props(['icon' => null])
<button type="button"
        {{ $attributes->merge(['class' => 'group inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-md transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring']) }}>
    @if($icon)
        <span class="shrink-0 w-4 h-4"><x-ui.icon :name="$icon" class="w-4 h-4" /></span>
    @endif
    <span>{{ $slot }}</span>
    <svg class="shrink-0 w-3 h-3 transition-transform duration-200"
         :class="openMenu === menuKey ? 'rotate-180' : ''"
         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
    </svg>
</button>
