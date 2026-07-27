@props([
    'variant' => 'default',
])

@php
$variantClasses = match($variant) {
    'circular' => 'rounded-full',
    'text' => 'rounded',
    default => 'rounded-md',
};
@endphp

<div role="status" aria-label="加载中" {{ $attributes->merge(['class' => 'animate-pulse']) }}>
    <div class="{{ $variantClasses }} bg-muted {{ $slot ? '' : 'h-4 w-[250px]' }}">
        @if($slot)
            <div class="opacity-0">{{ $slot }}</div>
        @else
            <div class="h-4 w-[250px]"></div>
        @endif
    </div>
</div>
