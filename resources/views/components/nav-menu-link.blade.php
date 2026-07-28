@props(['href' => '#', 'description' => null, 'active' => false])
<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'block rounded-md p-3 transition-colors hover:bg-accent hover:text-accent-foreground ' . ($active ? 'bg-accent text-accent-foreground' : '')]) }}>
    <div class="text-sm font-medium leading-none">{{ $slot }}</div>
    @if($description)
        <p class="mt-1.5 text-xs leading-snug text-muted-foreground">{{ $description }}</p>
    @endif
</a>
