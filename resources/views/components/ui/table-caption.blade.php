@props([
    'caption' => null,
])

@php
$captionClasses = 'mt-4 text-sm text-muted-foreground';
@endphp

{{ $slot }}

@if($caption)
    <caption class="{{ $captionClasses }}">{{ $caption }}</caption>
@endif
