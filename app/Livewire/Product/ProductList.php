<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithPagination;
    use WithRowSelection;
    use WithToast;

    protected string $modelClass = Product::class;

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

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

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
            $this->toastSuccess('商品已更新');
        } else {
            Product::create($data);
            $this->toastSuccess('商品已创建');
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
        $this->toastSuccess('商品已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterCategoryId = 0;
        $this->filterStatus = -1;
        $this->resetPage();
        $this->clearSelection();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
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

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '商品名', 'sortable' => true, 'exportable' => true],
            ['key' => 'category_id', 'label' => '分类', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'description', 'label' => '描述', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Product::with('category')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->filterCategoryId > 0, function ($q) {
                $q->where('category_id', $this->filterCategoryId);
            })
            ->when($this->filterStatus >= 0, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '商品_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Product::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商品名' => 'name',
            '分类ID' => 'category_id',
            '单位' => 'unit',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
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
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.product-list', compact('products', 'categories', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('商品管理');
    }
}
