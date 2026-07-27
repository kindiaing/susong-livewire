@props([])

<h2 {{ $attributes->merge(['class' => 'text-lg font-semibold leading-none tracking-tight text-foreground']) }}>
    {{ $slot }}
</h2>
