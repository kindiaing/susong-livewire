@props([])

<div {{ $attributes->merge(['class' => 'px-6']) }}>
    {{ $slot }}
</div>
