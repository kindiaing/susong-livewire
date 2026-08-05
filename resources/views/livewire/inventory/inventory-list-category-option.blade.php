@php
    $checkState = $categoryCheckStates[$cat->id] ?? 'unchecked';
    $isChecked = $checkState === 'checked';
@endphp

<label class="flex items-center gap-2 px-3 py-1.5 text-sm hover:bg-accent cursor-pointer transition-colors" style="padding-left: {{ 12 + $depth * 20 }}px;">
    <input type="checkbox"
           @checked($isChecked)
           wire:click="toggleCategoryFilter({{ $cat->id }})"
           x-init="$el.indeterminate = {{ $checkState === 'indeterminate' ? 'true' : 'false' }}"
           class="rounded" />
    <span class="truncate {{ $checkState === 'indeterminate' ? 'text-blue-600 font-medium' : '' }}">{{ $cat->name }}</span>
</label>
@if($cat->children->isNotEmpty())
    @foreach($cat->children as $child)
        @include('livewire.inventory.inventory-list-category-option', ['cat' => $child, 'depth' => $depth + 1])
    @endforeach
@endif
