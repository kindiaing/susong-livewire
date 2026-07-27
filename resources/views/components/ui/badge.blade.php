@props(['variant' => 'default'])

@php
$variantClasses = match($variant) {
    'default' => 'border-transparent bg-foreground text-background',
    'blue', 'info' => 'border-transparent bg-blue-600 text-white',
    'green', 'success' => 'border-transparent bg-green-600 text-white',
    'orange', 'warning' => 'border-transparent bg-orange-600 text-white',
    'red', 'destructive' => 'border-transparent bg-red-600 text-white',
    'yellow' => 'border-transparent bg-yellow-600 text-white',
    'purple' => 'border-transparent bg-purple-600 text-white',
    'outline' => 'border-border text-foreground',
    'outline-blue' => 'border-blue-600 text-blue-600',
    'outline-green' => 'border-green-600 text-green-600',
    'outline-orange' => 'border-orange-600 text-orange-600',
    'outline-red' => 'border-red-600 text-red-600',
    'secondary' => 'border-transparent bg-secondary text-secondary-foreground',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 $variantClasses"]) }}>
    {{ $slot }}
</span>
