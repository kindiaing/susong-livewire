@props([
    'currentPage' => 1,
    'totalPages' => 1,
    'total' => 0,
    'perPage' => 15,
    'showInfo' => true,
    'size' => 'default',
])

@php
$from = ($currentPage - 1) * $perPage + 1;
$to = min($currentPage * $perPage, $total);

$buttonSize = match($size) {
    'sm' => 'h-8 w-8 text-xs',
    'default' => 'h-9 w-9 text-sm',
    'lg' => 'h-10 w-10 text-sm',
};

$iconSize = match($size) {
    'sm' => 'h-3.5 w-3.5',
    'default' => 'h-4 w-4',
    'lg' => 'h-5 w-5',
};

// Build page range
$pages = [];
$start = max(1, $currentPage - 2);
$end = min($totalPages, $currentPage + 2);

if ($start > 1) {
    $pages[] = 1;
    if ($start > 2) $pages[] = '...';
}
for ($i = $start; $i <= $end; $i++) {
    $pages[] = $i;
}
if ($end < $totalPages) {
    if ($end < $totalPages - 1) $pages[] = '...';
    $pages[] = $totalPages;
}
@endphp

<nav role="navigation" aria-label="分页导航" {{ $attributes->merge(['class' => 'flex items-center justify-between']) }}>
    @if($showInfo)
        <div class="text-sm text-muted-foreground">
            显示第 <span class="font-medium text-foreground">{{ $from }}</span>
            - <span class="font-medium text-foreground">{{ $to }}</span>
            条，共 <span class="font-medium text-foreground">{{ $total }}</span> 条
        </div>
    @else
        <div></div>
    @endif

    <div class="flex items-center gap-1">
        {{-- Previous --}}
        <button
            type="button"
            @if($currentPage <= 1) disabled @endif
            wire:click="previousPage"
            class="inline-flex items-center justify-center rounded-md border border-input bg-background {{ $buttonSize }} transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50"
            aria-label="上一页"
        >
            <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Page Numbers --}}
        @foreach($pages as $page)
            @if($page === '...')
                <span class="inline-flex items-center justify-center {{ $buttonSize }} text-muted-foreground">...</span>
            @else
                <button
                    type="button"
                    wire:click="gotoPage({{ $page }})"
                    @if($page == $currentPage) aria-current="page" @endif
                    class="inline-flex items-center justify-center rounded-md border text-sm font-medium transition-colors {{ $page == $currentPage
                        ? 'border-blue-600 bg-blue-600 text-white hover:bg-blue-600/90'
                        : 'border-input bg-background hover:bg-accent hover:text-accent-foreground'
                    }} {{ $buttonSize }}"
                >
                    {{ $page }}
                </button>
            @endif
        @endforeach

        {{-- Next --}}
        <button
            type="button"
            @if($currentPage >= $totalPages) disabled @endif
            wire:click="nextPage"
            class="inline-flex items-center justify-center rounded-md border border-input bg-background {{ $buttonSize }} transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50"
            aria-label="下一页"
        >
            <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</nav>
