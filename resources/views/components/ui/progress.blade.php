@props([
    'value' => 0,
    'max' => 100,
    'variant' => 'default',
    'showValue' => false,
    'size' => 'default',
])

@php
$percentage = $max > 0 ? min(max(($value / $max) * 100, 0), 100) : 0;

$variantClasses = match($variant) {
    'blue' => 'bg-blue-600',
    'green' => 'bg-green-600',
    'orange' => 'bg-orange-600',
    'red', 'destructive' => 'bg-red-600',
    'yellow' => 'bg-yellow-600',
    'purple' => 'bg-purple-600',
    default => 'bg-foreground',
};

$sizeClasses = match($size) {
    'sm' => 'h-1.5',
    'default' => 'h-2.5',
    'lg' => 'h-4',
};
@endphp

<div {{ $attributes->merge(['class' => '']) }} role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="{{ $max }}">
    <div class="flex items-center gap-2">
        <div class="w-full overflow-hidden rounded-full bg-secondary {{ $sizeClasses }}">
            <div
                class="{{ $variantClasses }} {{ $sizeClasses }} rounded-full transition-all duration-500 ease-in-out"
                style="width: {{ $percentage }}%"
            ></div>
        </div>
        @if($showValue)
            <span class="text-sm font-medium text-muted-foreground whitespace-nowrap">{{ round($percentage) }}%</span>
        @endif
    </div>
</div>
