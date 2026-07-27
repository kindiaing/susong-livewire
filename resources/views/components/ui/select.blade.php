@props([
    'label' => null,
    'hint' => null,
    'error' => null,
    'disabled' => false,
    'placeholder' => '请选择...',
])

@php
$errorClasses = $error ? 'border-red-600 focus-visible:ring-red-600' : 'border-input focus-visible:ring-ring';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed bg-muted' : '';

// Extract wire:* and x-* attributes for the <select> element
$selectWireAttrs = $attributes->whereStartsWith('wire:');
$selectXAttrs = $attributes->whereStartsWith('x-');
$selectOtherAttrs = $attributes->whereDoesntStartWith('wire:')->whereDoesntStartWith('x-')->except('class');
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:')->whereDoesntStartWith('x-')->merge(['class' => 'grid gap-1.5']) }}>
    @if($label)
        <label class="text-sm font-medium leading-none text-foreground">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <select
            @if($disabled) disabled @endif
            @if($error) aria-invalid="true" @endif
            {{ $selectWireAttrs }}
            {{ $selectXAttrs }}
            {{ $selectOtherAttrs->merge(['class' => 'flex h-9 w-full appearance-none items-center rounded-md border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 transition-colors ' . $errorClasses . ' ' . $disabledClasses]) }}
        >
            @if($placeholder)
                <option value="" disabled selected>{{ $placeholder }}</option>
            @endif
            {{ $slot }}
        </select>

        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>

    @if($error)
        <p class="text-xs text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
