@props([
    'orientation' => 'horizontal',
])

@php
$classes = match($orientation) {
    'horizontal' => 'h-px w-full bg-border',
    'vertical' => 'h-full w-px bg-border',
};
@endphp

<div
    role="separator"
    aria-orientation="{{ $orientation }}"
    {{ $attributes->merge(['class' => 'shrink-0 ' . $classes]) }}
></div>
