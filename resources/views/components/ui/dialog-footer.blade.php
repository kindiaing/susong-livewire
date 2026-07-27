@props([])

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-2 p-6 pt-4']) }}>
    {{ $slot }}
</div>
