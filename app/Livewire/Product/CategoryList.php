<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;

    protected string $modelClass = Category::class;

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
        $this->formParentId = '0';
        $this->formName = '';
        $this->formIcon = '';
        $this->formSort = 0;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'parent_id', 'label' => '上级ID', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'icon', 'label' => '图标', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Category::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%");
        })->orderBy('sort')->orderBy('id');
    }

    public function getExportFileName(): string
    {
        return '分类_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Category::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '名称' => 'name',
            '上级ID' => 'parent_id',
            '排序' => 'sort',
            '状态' => 'status',
            '图标' => 'icon',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Category::with('parent')->orderBy('sort')->orderBy('id');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $categories = $query->paginate(20);

        $parentOptions = Category::orderBy('sort')->orderBy('id')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.category-list', compact('categories', 'parentOptions', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('分类管理');
    }
}
