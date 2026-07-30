<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Keyword;
use Livewire\Component;
use Livewire\WithPagination;

class KeywordList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = Keyword::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formKeyword = '';
    public int $formProductId = 0;
    public int $formSearchCount = 0;

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
            $this->toastSuccess('关键词已更新');
        } else {
            Keyword::create($data);
            $this->toastSuccess('关键词已创建');
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
        $this->toastSuccess('关键词已删除');
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
        $this->formKeyword = '';
        $this->formProductId = 0;
        $this->formSearchCount = 0;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'word', 'label' => '关键词', 'sortable' => true, 'exportable' => true],
            ['key' => 'search_count', 'label' => '搜索次数', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Keyword::with('product')
            ->when($this->search, function ($q) {
                $q->where('keyword', 'like', "%{$this->search}%");
            })
            ->orderBy('search_count', 'desc')
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '关键词_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Keyword::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '关键词' => 'keyword',
            '商品ID' => 'product_id',
            '搜索次数' => 'search_count',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Keyword::with('product')->orderBy('search_count', 'desc')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('keyword', 'like', "%{$this->search}%");
        }

        $keywords = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.keyword-list', compact('keywords', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('关键词管理');
    }
}
