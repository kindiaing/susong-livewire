@props([])

<nav {{ $attributes->merge(['class' => 'flex items-center gap-1.5 text-sm text-muted-foreground']) }}>
    {{ $slot }}
</nav>
