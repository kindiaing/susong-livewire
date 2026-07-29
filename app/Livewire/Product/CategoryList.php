<?php

namespace App\Livewire\Product;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formParentId = '0';
    public string $formName = '';
    public string $formIcon = '';
    public int $formSort = 0;
    public int $formStatus = 1;

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $id;
        $this->formParentId = (string) $category->parent_id;
        $this->formName = $category->name;
        $this->formIcon = $category->icon ?? '';
        $this->formSort = $category->sort;
        $this->formStatus = $category->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formParentId' => 'required|integer',
            'formName' => 'required|string|max:50',
            'formIcon' => 'nullable|string|max:255',
            'formSort' => 'required|integer|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'parent_id' => (int) $validated['formParentId'],
            'name' => $validated['formName'],
            'icon' => $validated['formIcon'] ?: null,
            'sort' => $validated['formSort'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '分类已更新', type: 'success');
        } else {
            Category::create($data);
            $this->dispatch('toast', message: '分类已创建', type: 'success');
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
        Category::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '分类已删除', type: 'success');
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
        $this->formParentId = '0';
        $this->formName = '';
        $this->formIcon = '';
        $this->formSort = 0;
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = Category::with('parent')->orderBy('sort')->orderBy('id');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $categories = $query->paginate(20);

        $parentOptions = Category::orderBy('sort')->orderBy('id')->get();

        return view('livewire.product.category-list', compact('categories', 'parentOptions'))
            ->layout('components.app-layout')
            ->title('分类管理');
    }
}
