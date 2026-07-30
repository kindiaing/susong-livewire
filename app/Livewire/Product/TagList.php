<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class TagList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = Tag::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formName = '';
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
            $this->toastSuccess('标签已更新');
        } else {
            Tag::create($data);
            $this->toastSuccess('标签已创建');
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
        $this->toastSuccess('标签已删除');
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
        $this->formName = '';
        $this->formSort = 0;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '类型', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Tag::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%");
        })->orderBy('sort')->orderBy('id');
    }

    public function getExportFileName(): string
    {
        return '标签_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Tag::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '名称' => 'name',
            '类型' => 'type',
            '排序' => 'sort',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Tag::orderBy('sort')->orderBy('id');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $tags = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.tag-list', compact('tags', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('标签管理');
    }
}
