<?php

namespace App\Livewire\Product;

use App\Models\Keyword;
use Livewire\Component;
use Livewire\WithPagination;

class KeywordList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formKeyword = '';
    public int $formProductId = 0;
    public int $formSearchCount = 0;

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $keyword = Keyword::findOrFail($id);
        $this->editingId = $id;
        $this->formKeyword = $keyword->keyword;
        $this->formProductId = $keyword->product_id ?? 0;
        $this->formSearchCount = $keyword->search_count;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formKeyword' => 'required|string|max:50',
            'formProductId' => 'nullable|integer',
            'formSearchCount' => 'required|integer|min:0',
        ]);

        $data = [
            'keyword' => $validated['formKeyword'],
            'product_id' => $validated['formProductId'] ?: null,
            'search_count' => $validated['formSearchCount'],
        ];

        if ($this->editingId) {
            Keyword::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '关键词已更新', type: 'success');
        } else {
            Keyword::create($data);
            $this->dispatch('toast', message: '关键词已创建', type: 'success');
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
        Keyword::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '关键词已删除', type: 'success');
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
        $this->formKeyword = '';
        $this->formProductId = 0;
        $this->formSearchCount = 0;
    }

    public function render()
    {
        $query = Keyword::with('product')->orderBy('search_count', 'desc')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('keyword', 'like', "%{$this->search}%");
        }

        $keywords = $query->paginate(20);

        return view('livewire.product.keyword-list', compact('keywords'))
            ->layout('components.app-layout')
            ->title('关键词管理');
    }
}
