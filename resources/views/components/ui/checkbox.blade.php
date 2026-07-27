@props([
    'label' => null,
    'checked' => false,
    'disabled' => false,
    'id' => null,
])

@php
$id = $id ?? 'checkbox-' . \Illuminate\Support\Str::random(8);
@endphp

<div class="flex items-center gap-2">
    <div class="relative flex items-center">
        <input
            type="checkbox"
            id="{{ $id }}"
            @if($checked) checked @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'peer h-4 w-4 shrink-0 rounded border border-input accent-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) }}
        />
    </div>
    @if($label)
        <label for="{{ $id }}" class="text-sm font-medium leading-none text-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-70 {{ $disabled ? 'cursor-not-allowed opacity-50' : '' }}">
            {{ $label }}
        </label>
    @endif
</div>
