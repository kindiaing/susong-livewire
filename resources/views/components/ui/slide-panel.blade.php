@props([
    'open' => false,
    'title' => '',
    'width' => '400px',
    'closeOnOutside' => true,
    'storeKey' => '',  {{-- 受控模式：Alpine store key，如 'uiSettings' --}}
])

@php
// 受控模式：由外部 Alpine store 控制开关
// 非受控模式：组件内部 panelOpen 自管理
$isControlled = filled($storeKey);
@endphp

@if($isControlled)
{{-- 受控模式：使用外部 store --}}
<div x-data x-cloak>
    <template x-teleport="body">
        <div x-show="$store.{{ $storeKey }}.open" class="fixed inset-0 z-50">
            {{-- 遮罩 --}}
            <div class="fixed inset-0 bg-black/40"
                 x-show="$store.{{ $storeKey }}.open"
                 x-transition:enter="transition-opacity ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in-out duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="{{ $closeOnOutside ? "\$store.{$storeKey}.open = false" : '' }}"></div>

            {{-- 右侧面板 --}}
            <div class="fixed right-0 inset-y-0 bg-background border-l border-border shadow-xl flex flex-col"
                 style="width: {{ $width }};"
                 x-show="$store.{{ $storeKey }}.open"
                 x-transition:enter="transition-transform ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition-transform ease-in-out duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                @if($title)
                <div class="flex items-center justify-between px-5 py-4 border-b border-border shrink-0">
                    <h3 class="text-sm font-semibold">{{ $title }}</h3>
                    <button type="button" @click="$store.{{ $storeKey }}.open = false" class="rounded-sm p-1 hover:bg-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endif

                <div class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>

@else
{{-- 非受控模式：内部管理开关状态 --}}
<div
    x-data="{ panelOpen: {{ $open ? 'true' : 'false' }} }"
    @keydown.escape.window="if(panelOpen) panelOpen = false"
>
    {{-- Trigger --}}
    <div @click="panelOpen = true">{{ $slot->trigger ?? '' }}</div>

    {{-- Portal --}}
    <template x-teleport="body">
        <div x-show="panelOpen" class="fixed inset-0 z-50" x-cloak>
            {{-- 遮罩 --}}
            <div class="fixed inset-0 bg-black/40"
                 x-show="panelOpen"
                 x-transition:enter="transition-opacity ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in-out duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="{{ $closeOnOutside ? 'panelOpen = false' : '' }}"></div>

            {{-- 右侧面板 --}}
            <div class="fixed right-0 inset-y-0 bg-background border-l border-border shadow-xl flex flex-col"
                 style="width: {{ $width }};"
                 x-show="panelOpen"
                 x-transition:enter="transition-transform ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition-transform ease-in-out duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                @if($title)
                <div class="flex items-center justify-between px-5 py-4 border-b border-border shrink-0">
                    <h3 class="text-sm font-semibold">{{ $title }}</h3>
                    <button type="button" @click="panelOpen = false" class="rounded-sm p-1 hover:bg-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endif

                <div class="flex-1 overflow-y-auto">
                    {{ $slot->panelContent ?? '' }}
                </div>
            </div>
        </div>
    </template>
</div>
@endif
