@props([
    'label' => null,
    'hint' => null,
    'error' => null,
    'rows' => 3,
    'disabled' => false,
    'readonly' => false,
])

@php
$errorClasses = $error ? 'border-red-600 focus-visible:ring-red-600' : 'border-input focus-visible:ring-ring';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed bg-muted' : '';

// Extract wire:* and x-* attributes for the <textarea> element
$textareaWireAttrs = $attributes->whereStartsWith('wire:');
$textareaXAttrs = $attributes->whereStartsWith('x-');
$textareaOtherAttrs = $attributes->whereDoesntStartWith('wire:')->whereDoesntStartWith('x-')->except('class');
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:')->whereDoesntStartWith('x-')->merge(['class' => 'grid gap-1.5']) }}>
    @if($label)
        <label class="text-sm font-medium leading-none text-foreground">
            {{ $label }}
        </label>
    @endif

    <textarea
        rows="{{ $rows }}"
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($error) aria-invalid="true" @endif
        {{ $textareaWireAttrs }}
        {{ $textareaXAttrs }}
        {{ $textareaOtherAttrs->merge(['class' => 'flex min-h-[80px] w-full rounded-md border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 transition-colors ' . $errorClasses . ' ' . $disabledClasses]) }}
    >{{ $slot }}</textarea>

    @if($error)
        <p class="text-xs text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
