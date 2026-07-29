<?php

namespace App\Livewire\Product;

use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class TagList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formName = '';
    public int $formSort = 0;
    public int $formStatus = 1;

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $tag->name;
        $this->formSort = $tag->sort;
        $this->formStatus = $tag->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:50',
            'formSort' => 'required|integer|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'name' => $validated['formName'],
            'sort' => $validated['formSort'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            Tag::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '标签已更新', type: 'success');
        } else {
            Tag::create($data);
            $this->dispatch('toast', message: '标签已创建', type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        Tag::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '标签已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formSort = 0;
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = Tag::orderBy('sort')->orderBy('id');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $tags = $query->paginate(20);

        return view('livewire.product.tag-list', compact('tags'))
            ->layout('components.app-layout')
            ->title('标签管理');
    }
}
