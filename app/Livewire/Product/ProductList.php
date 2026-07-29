<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterCategoryId = 0;
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formCategoryId = 0;
    public string $formName = '';
    public string $formUnit = '';
    public int $formIsWeightPriced = 0;
    public int $formStockWarningValue = 0;
    public int $formStatus = 1;
    public string $formDescription = '';

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editingId = $id;
        $this->formCategoryId = $product->category_id;
        $this->formName = $product->name;
        $this->formUnit = $product->unit;
        $this->formIsWeightPriced = $product->is_weight_priced;
        $this->formStockWarningValue = $product->stock_warning_value;
        $this->formStatus = $product->status;
        $this->formDescription = $product->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formCategoryId' => 'required|integer|min:1',
            'formName' => 'required|string|max:100',
            'formUnit' => 'required|string|max:20',
            'formIsWeightPriced' => 'required|in:0,1',
            'formStockWarningValue' => 'required|integer|min:0',
            'formStatus' => 'required|in:0,1',
            'formDescription' => 'nullable|string|max:2000',
        ]);

        $data = [
            'category_id' => $validated['formCategoryId'],
            'name' => $validated['formName'],
            'unit' => $validated['formUnit'],
            'is_weight_priced' => $validated['formIsWeightPriced'],
            'stock_warning_value' => $validated['formStockWarningValue'],
            'status' => $validated['formStatus'],
            'description' => $validated['formDescription'] ?: null,
        ];

        if ($this->editingId) {
            Product::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '商品已更新', type: 'success');
        } else {
            Product::create($data);
            $this->dispatch('toast', message: '商品已创建', type: 'success');
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
        Product::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '商品已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterCategoryId = 0;
        $this->filterStatus = -1;
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formCategoryId = 0;
        $this->formName = '';
        $this->formUnit = '';
        $this->formIsWeightPriced = 0;
        $this->formStockWarningValue = 0;
        $this->formStatus = 1;
        $this->formDescription = '';
    }

    public function render()
    {
        $query = Product::with('category')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterCategoryId > 0) {
            $query->where('category_id', $this->filterCategoryId);
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $products = $query->paginate(20);
        $categories = Category::orderBy('sort')->orderBy('id')->get();

        return view('livewire.product.product-list', compact('products', 'categories'))
            ->layout('components.app-layout')
            ->title('商品管理');
    }
}
