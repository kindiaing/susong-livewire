@props([
    'type' => 'text',
    'label' => null,
    'hint' => null,
    'error' => null,
    'size' => 'default',
    'prefix' => null,
    'suffix' => null,
    'disabled' => false,
    'readonly' => false,
])

@php
$sizeClasses = match($size) {
    'sm' => 'h-8 px-3 text-xs',
    'default' => 'h-9 px-3 text-sm',
    'lg' => 'h-10 px-4 text-sm',
};

$errorClasses = $error ? 'border-red-600 focus-visible:ring-red-600' : 'border-input focus-visible:ring-ring';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed bg-muted' : '';
@endphp

<div {{ $attributes->merge(['class' => 'grid gap-1.5']) }}>
    @if($label)
        <label class="text-sm font-medium leading-none text-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
            {{ $label }}
        </label>
    @endif

    <div class="flex items-center rounded-md border {{ $errorClasses }} {{ $disabledClasses }} bg-background transition-colors focus-within:ring-2 focus-within:ring-offset-2">
        @if($prefix)
            <span class="flex items-center pl-3 text-sm text-muted-foreground">
                {{ $prefix }}
            </span>
        @endif

        <input
            type="{{ $type }}"
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($error) aria-invalid="true" aria-describedby="{{ $attributes->get('id', '') }}-error" @endif
            {{ $attributes->except('class')->merge(['class' => 'flex-1 bg-transparent file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none disabled:cursor-not-allowed ' . $sizeClasses]) }}
        />

        @if($suffix)
            <span class="flex items-center pr-3 text-sm text-muted-foreground">
                {{ $suffix }}
            </span>
        @endif
    </div>

    @if($error)
        <p id="{{ $attributes->get('id', '') }}-error" class="text-xs text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
