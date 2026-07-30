<?php

namespace App\Livewire\Inventory;

use App\Models\Warehouse;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class WarehouseList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = Warehouse::class;

    public string $search = '';
    public int $filterType = -1;
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formName = '';
    public int $formType = 1;
    public int $formIsColdChain = 0;
    public string $formAddress = '';
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
        $item = Warehouse::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $item->name;
        $this->formType = $item->type;
        $this->formIsColdChain = $item->is_cold_chain;
        $this->formAddress = $item->address ?? '';
        $this->formStatus = $item->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:50',
            'formType' => 'required|in:1,2',
            'formIsColdChain' => 'required|in:0,1',
            'formAddress' => 'nullable|string|max:255',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'name' => $validated['formName'],
            'type' => $validated['formType'],
            'is_cold_chain' => $validated['formIsColdChain'],
            'address' => $validated['formAddress'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $item = Warehouse::findOrFail($this->editingId);
            $item->update($data);
            $this->toastSuccess('仓库已更新');
        } else {
            Warehouse::create($data);
            $this->toastSuccess('仓库已创建');
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
        $item = Warehouse::findOrFail($this->deletingId);
        $item->delete();
        $this->toastSuccess('仓库已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterType = -1;
        $this->filterStatus = -1;
        $this->resetPage();
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
        $this->formType = 1;
        $this->formIsColdChain = 0;
        $this->formAddress = '';
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '仓库名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '仓库类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'is_cold_chain', 'label' => '冷链', 'sortable' => true, 'exportable' => true],
            ['key' => 'address', 'label' => '地址', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Warehouse::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '仓库管理_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Warehouse::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '仓库名称' => 'name',
            '仓库类型' => 'type',
            '冷链' => 'is_cold_chain',
            '地址' => 'address',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return Warehouse::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Warehouse::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterType >= 0) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.inventory.warehouse-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('仓库管理');
    }
}
