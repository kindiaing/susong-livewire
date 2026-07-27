@props([
    'src' => null,
    'alt' => '',
    'size' => 'default',
    'fallback' => null,
])

@php
$sizeClasses = match($size) {
    'xs' => 'h-6 w-6 text-[10px]',
    'sm' => 'h-8 w-8 text-xs',
    'default' => 'h-10 w-10 text-sm',
    'lg' => 'h-12 w-12 text-base',
    'xl' => 'h-16 w-16 text-lg',
};

$initials = '';
if (!$src && $fallback) {
    $parts = explode(' ', $fallback);
    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}
@endphp

<div class="relative inline-flex items-center justify-center shrink-0 overflow-hidden rounded-full bg-muted {{ $sizeClasses }}" {{ $attributes }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}" class="aspect-square h-full w-full object-cover" />
    @elseif($initials)
        <span class="font-medium text-muted-foreground">{{ $initials }}</span>
    @else
        <svg class="h-1/2 w-1/2 text-muted-foreground/50" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
    @endif
</div>
