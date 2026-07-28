@props(['for' => null])

<label @if($for) for="{{ $for }}" @endif
       class="text-sm font-medium leading-none text-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
       {{ $attributes }}>
    {{ $slot }}
</label>
