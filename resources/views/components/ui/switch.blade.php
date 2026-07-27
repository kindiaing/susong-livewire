@props([
    'label' => null,
    'checked' => false,
    'disabled' => false,
    'id' => null,
    'size' => 'default',
])

@php
$id = $id ?? 'switch-' . \Illuminate\Support\Str::random(8);

$trackSize = match($size) {
    'sm' => 'h-4 w-7',
    'default' => 'h-5 w-9',
    'lg' => 'h-6 w-11',
};

$thumbSize = match($size) {
    'sm' => 'h-3 w-3',
    'default' => 'h-4 w-4',
    'lg' => 'h-5 w-5',
};

$thumbTranslate = match($size) {
    'sm' => 'x-data.checked ? "translate-x-3" : "translate-x-0.5"',
    'default' => 'x-data.checked ? "translate-x-4" : "translate-x-0.5"',
    'lg' => 'x-data.checked ? "translate-x-5" : "translate-x-0.5"',
};
@endphp

<div x-data="{ checked: {{ $checked ? 'true' : 'false' }} }" class="flex items-center gap-2">
    <button
        type="button"
        role="switch"
        :aria-checked="checked"
        id="{{ $id }}"
        @if($disabled) disabled @endif
        @click="checked = !checked"
        x-model="checked"
        {{ $attributes->except('class')->merge(['class' => 'inline-flex shrink-0 cursor-pointer items-center rounded-full border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ' . $trackSize . ' ' . ($checked ? 'bg-blue-600' : 'bg-input')]) }}
        :class="checked ? 'bg-blue-600' : 'bg-input'"
    >
        <span
            :class="checked ? 'translate-x-4' : 'translate-x-0.5'"
            class="pointer-events-none block rounded-full bg-white shadow-lg ring-0 transition-transform {{ $thumbSize }}"
        ></span>
    </button>

    @if($label)
        <label for="{{ $id }}" class="text-sm font-medium leading-none text-foreground {{ $disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
            {{ $label }}
        </label>
    @endif
</div>
