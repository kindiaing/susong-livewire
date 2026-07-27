@props([
    'variant' => 'default',
    'size' => 'default',
    'href' => null,
    'disabled' => false,
    'icon' => false,
    'loading' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

$variantClasses = match($variant) {
    'default' => 'bg-foreground text-background hover:bg-foreground/90',
    'blue' => 'bg-blue-600 text-white hover:bg-blue-600/90',
    'green' => 'bg-green-600 text-white hover:bg-green-600/90',
    'orange' => 'bg-orange-600 text-white hover:bg-orange-600/90',
    'red', 'destructive' => 'bg-red-600 text-white hover:bg-red-600/90',
    'yellow' => 'bg-yellow-600 text-white hover:bg-yellow-600/90',
    'purple' => 'bg-purple-600 text-white hover:bg-purple-600/90',
    'outline' => 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
    'outline-blue' => 'border border-blue-600 text-blue-600 bg-background hover:bg-blue-50',
    'outline-green' => 'border border-green-600 text-green-600 bg-background hover:bg-green-50',
    'outline-orange' => 'border border-orange-600 text-orange-600 bg-background hover:bg-orange-50',
    'outline-red' => 'border border-red-600 text-red-600 bg-background hover:bg-red-50',
    'ghost' => 'hover:bg-accent hover:text-accent-foreground',
    'link' => 'text-blue-600 underline-offset-4 hover:underline',
    'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
};

$sizeClasses = match($size) {
    'xs' => 'h-7 px-2.5 text-xs rounded',
    'sm' => 'h-8 px-3 text-xs',
    'default' => 'h-9 px-4 py-2',
    'lg' => 'h-10 px-6',
    'icon' => 'h-9 w-9',
};

$classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses;

// Loading spinner SVG
$spinnerSvg = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

$isDisabled = $disabled || $loading;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($isDisabled) disabled aria-disabled="true" @endif>
        @if($loading) {!! $spinnerSvg !!} @elseif(isset($slot->iconLeft) && $slot->iconLeft->isNotEmpty()) <span class="shrink-0">{{ $slot->iconLeft }}</span> @endif
        {{ $slot }}
        @if(!$loading && isset($slot->iconRight) && $slot->iconRight->isNotEmpty()) <span class="shrink-0">{{ $slot->iconRight }}</span> @endif
    </a>
@else
    <button
        {{ $attributes->merge(['class' => $classes]) }}
        @if($isDisabled) disabled @endif
        @if($loading) aria-busy="true" @endif
    >
        @if($loading)
            {!! $spinnerSvg !!}
        @else
            @if(isset($slot->iconLeft) && $slot->iconLeft->isNotEmpty())
                <span class="shrink-0">{{ $slot->iconLeft }}</span>
            @endif
        @endif
        {{ $slot }}
        @if(!$loading && isset($slot->iconRight) && $slot->iconRight->isNotEmpty())
            <span class="shrink-0">{{ $slot->iconRight }}</span>
        @endif
    </button>
@endif
