@props([
    'label' => null,
    'hint' => null,
    'error' => null,
    'placeholder' => '请选择...',
    'options' => [],
    'clearable' => false,
    'disabled' => false,
    'value' => null,
    'wireModel' => null,
])

@php
// Resolve current label from options + value
$currentValue = ($value && $value != 0) ? (string)$value : '';
$currentLabel = '';
if ($currentValue !== '') {
    $match = collect($options)->first(fn($opt) => (string)$opt['value'] === $currentValue);
    $currentLabel = $match ? $match['label'] : '';
}
$optionsJson = collect($options)->map(fn($opt) => [
    'value' => (string)($opt['value'] ?? ''),
    'label' => (string)($opt['label'] ?? ''),
])->values()->toJson();

// Build wrapper attrs (exclude wire: and x- since we handle them via wireModel)
$wrapperAttrs = $attributes->whereDoesntStartWith('wire:')->whereDoesntStartWith('x-')->except('class');
@endphp

<div x-data="window.__searchableSelect({
    open: false,
    search: '',
    activeIndex: -1,
    selectedValue: '{{ $currentValue }}',
    selectedLabel: '{{ $currentLabel }}',
    options: {{ $optionsJson }},
    wireModelProperty: '{{ $wireModel ?? '' }}'
})"
    @click.outside="open = false"
    {{ $wrapperAttrs->merge(['class' => 'grid gap-1.5']) }}>

    @if($label)
    <label class="text-sm font-medium leading-none text-foreground">{{ $label }}</label>
    @endif

    <div class="relative">
        {{-- Trigger button --}}
        <button type="button"
                @click="toggle()"
                @if($disabled) disabled @endif
                class="flex h-9 w-full items-center justify-between rounded-md border bg-background px-3 text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 {{ $error ? 'border-red-600 focus-visible:ring-red-600' : 'border-input' }} {{ $disabled ? 'opacity-50 cursor-not-allowed bg-muted' : 'cursor-pointer' }}">
            <span x-text="hasSelection() ? selectedLabel : '{{ $placeholder }}'"
                  :class="hasSelection() ? 'text-foreground' : 'text-muted-foreground'"
                  class="truncate text-left flex-1"></span>
            <svg class="w-4 h-4 text-muted-foreground shrink-0 ml-2 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Clear button --}}
        @if($clearable)
        <button type="button"
                x-show="hasSelection() && !{{ $disabled ? 'true' : 'false' }}"
                @click.prevent="clearValue()"
                class="absolute right-8 top-1/2 -translate-y-1/2 flex items-center justify-center w-5 h-5 rounded-sm hover:bg-muted text-muted-foreground hover:text-foreground transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        @endif

        {{-- Dropdown panel --}}
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute z-50 mt-1 w-full rounded-md border bg-popover text-popover-foreground shadow-md overflow-hidden">

            {{-- Search input --}}
            <div class="flex items-center border-b px-3">
                <svg class="w-4 h-4 text-muted-foreground shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text"
                       x-model="search"
                       x-ref="searchInput"
                       @keydown.down.prevent="navigateDown()"
                       @keydown.up.prevent="navigateUp()"
                       @keydown.enter.prevent="selectActive()"
                       @keydown.escape="open = false"
                       class="flex-1 h-9 bg-transparent px-2 text-sm outline-none placeholder:text-muted-foreground"
                       placeholder="搜索..." />
            </div>

            {{-- Options list --}}
            <div class="max-h-60 overflow-y-auto py-1" x-ref="optionList">
                <template x-for="(opt, idx) in filteredOptions()" :key="opt.value">
                    <button type="button"
                            @click="selectOption(opt)"
                            @mouseenter="activeIndex = idx"
                            :data-option-idx="idx"
                            :class="{
                                'bg-accent text-accent-foreground': activeIndex === idx,
                                'text-popover-foreground': activeIndex !== idx
                            }"
                            class="flex w-full items-center px-3 py-2 text-sm hover:bg-accent hover:text-accent-foreground transition-colors cursor-pointer">
                        <span x-text="opt.label" class="truncate flex-1 text-left"></span>
                        <svg x-show="selectedValue == opt.value" class="w-4 h-4 ml-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-9"/>
                        </svg>
                    </button>
                </template>

                {{-- No results --}}
                <div x-show="filteredOptions().length === 0" class="px-3 py-4 text-center text-sm text-muted-foreground">
                    无匹配结果
                </div>
            </div>
        </div>
    </div>

    @if($error)
    <p class="text-xs text-red-600">{{ $error }}</p>
    @elseif($hint)
    <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
