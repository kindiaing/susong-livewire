@props(['href' => null, 'active' => false])

@if($active)
    <span class="font-medium text-foreground" aria-current="page">{{ $slot }}</span>
@elseif($href)
    <a href="{{ $href }}" class="transition-colors hover:text-foreground">{{ $slot }}</a>
@else
    <span>{{ $slot }}</span>
@endif
