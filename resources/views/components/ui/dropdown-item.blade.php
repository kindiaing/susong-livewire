@props([
    'href' => null,
    'disabled' => false,
    'variant' => 'default',
])

@php
$variantClasses = match($variant) {
    'destructive' => 'text-red-600 focus:bg-red-50',
    default => 'focus:bg-accent focus:text-accent-foreground',
};

$baseClasses = 'relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors';

$disabledClasses = $disabled ? 'pointer-events-none opacity-50' : 'cursor-pointer';
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $baseClasses }} {{ $variantClasses }} {{ $disabledClasses }}">
        {{ $slot }}
    </a>
@else
    <div role="menuitem" @if($disabled) aria-disabled="true" @endif class="{{ $baseClasses }} {{ $variantClasses }} {{ $disabledClasses }}">
        {{ $slot }}
    </div>
@endif
