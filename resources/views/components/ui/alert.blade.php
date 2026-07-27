@props(['variant' => 'info'])

@php
$variantClasses = match($variant) {
    'info', 'blue' => 'border-l-4 border-blue-600 bg-blue-50 text-blue-600 [&_strong]:text-blue-600',
    'success', 'green' => 'border-l-4 border-green-600 bg-green-50 text-green-600 [&_strong]:text-green-600',
    'warning', 'orange' => 'border-l-4 border-orange-600 bg-orange-50 text-orange-600 [&_strong]:text-orange-600',
    'destructive', 'red' => 'border-l-4 border-red-600 bg-red-50 text-red-600 [&_strong]:text-red-600',
    'purple' => 'border-l-4 border-purple-600 bg-purple-50 text-purple-600 [&_strong]:text-purple-600',
    default => 'border-l-4 border-foreground/20 bg-muted text-muted-foreground [&_strong]:text-foreground',
};

$iconSvg = match($variant) {
    'info', 'blue' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'success', 'green' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'warning', 'orange' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
    'destructive', 'red' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'purple' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
    default => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
};
@endphp

<div {{ $attributes->merge(['class' => "rounded-md p-4 $variantClasses"]) }}>
    <div class="flex items-start gap-3">
        {!! $iconSvg !!}
        <div class="flex-1 text-sm [&_p]:leading-relaxed">
            {{ $slot }}
        </div>
    </div>
</div>
