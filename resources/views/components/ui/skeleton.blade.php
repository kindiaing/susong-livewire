@props([
    'variant' => 'default',
    'lines' => null,
])

@php
// variant: default (rect) / circular / text / card
// lines: 文本行数（text 变体时使用）
@endphp

@if($variant === 'text' && $lines)
    <div class="space-y-2" role="status" aria-label="加载中">
        @for($i = 0; $i < $lines; $i++)
            <div class="animate-pulse rounded bg-muted {{ $i === $lines - 1 ? 'h-3 w-3/4' : 'h-3 w-full' }}"></div>
        @endfor
    </div>
@elseif($variant === 'circular')
    <div class="animate-pulse rounded-full bg-muted" role="status" aria-label="加载中" {{ $attributes->merge(['class' => '']) }}>
        <div {{ $attributes->merge(['class' => 'animate-pulse rounded-full bg-muted']) }}></div>
    </div>
@elseif($variant === 'card')
    <div class="animate-pulse rounded-lg border border-border p-4 space-y-3" role="status" aria-label="加载中">
        <div class="h-4 w-1/3 rounded bg-muted"></div>
        <div class="h-3 w-full rounded bg-muted"></div>
        <div class="h-3 w-2/3 rounded bg-muted"></div>
    </div>
@else
    <div class="animate-pulse rounded-md bg-muted" role="status" aria-label="加载中" {{ $attributes }}>
        @if($slot->isNotEmpty())
            <div class="opacity-0">{{ $slot }}</div>
        @else
            <div class="h-4 w-[250px]"></div>
        @endif
    </div>
@endif
