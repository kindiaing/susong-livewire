<?php

namespace App\Livewire\Inventory;

use App\Models\Warehouse;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class WarehouseList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Warehouse::class;

    public string $search = '';
    public int $filterType = -1;
    public int $filterStatus = -1;

    public string $formName = '';
    public int $formType = 1;
    public int $formIsColdChain = 0;
    public string $formAddress = '';
    public int $formSort = 0;
    public int $formStatus = 1;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $item = Warehouse::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $item->name;
        $this->formType = $item->type;
        $this->formIsColdChain = $item->is_cold_chain;
        $this->formAddress = $item->address ?? '';
        $this->formSort = $item->sort ?? 0;
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
            'formSort' => 'nullable|integer|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'name' => $validated['formName'],
            'type' => $validated['formType'],
            'is_cold_chain' => $validated['formIsColdChain'],
            'address' => $validated['formAddress'],
            'sort' => $validated['formSort'],
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

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formType = 1;
        $this->formIsColdChain = 0;
        $this->formAddress = '';
        $this->formSort = 0;
        $this->formStatus = 1;
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'type', 'is_cold_chain', 'address', 'status', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'type' => $row->type,
                'is_cold_chain' => $row->is_cold_chain,
                'address' => $row->address ?? '',
                'sort' => $row->sort,
                'status' => $row->status,
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['name'];
    }

    public function getImportRequiredFields(): array
    {
        return ['仓库名称'];
    }

    public function getImportValueMap(): array
    {
        return [
            'type' => ['常温' => 1, '冷藏' => 2],
            'is_cold_chain' => ['否' => 0, '是' => 1],
            'status' => ['禁用' => 0, '启用' => 1],
        ];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '仓库名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '仓库类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'is_cold_chain', 'label' => '冷链', 'sortable' => true, 'exportable' => true],
            ['key' => 'address', 'label' => '地址', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Warehouse::orderBy('sort')->orderBy('id', 'desc');
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
            '排序' => 'sort',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return Warehouse::orderBy('sort')->orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Warehouse::orderBy('sort')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterType >= 0) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.inventory.warehouse-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('仓库管理');
    }
}
