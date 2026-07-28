@props(['href' => '#', 'icon' => null, 'active' => false])
<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-md p-2.5 transition-colors hover:bg-accent hover:text-accent-foreground ' . ($active ? 'bg-accent text-accent-foreground' : '')]) }}>
    @if($icon)
        <span class="shrink-0 w-5 h-5 text-muted-foreground"><x-ui.icon :name="$icon" class="w-5 h-5" /></span>
    @endif
    <span class="text-sm font-medium leading-none">{{ $slot }}</span>
</a>
