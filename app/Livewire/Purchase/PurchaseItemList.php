<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseItem;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseItemList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = PurchaseItem::class;

    public string $search = '';
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formSkuId = 0;
    public int $formQuantity = 0;
    public int $formSourceType = 1;

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
        $item = PurchaseItem::findOrFail($id);
        $this->editingId = $id;
        $this->formSkuId = $item->sku_id;
        $this->formQuantity = $item->quantity;
        $this->formSourceType = $item->source_type;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSkuId' => 'required|integer|min:1',
            'formQuantity' => 'required|integer|min:1',
            'formSourceType' => 'required|in:1,2',
        ]);

        $data = [
            'sku_id' => $validated['formSkuId'],
            'quantity' => $validated['formQuantity'],
            'source_type' => $validated['formSourceType'],
        ];

        if ($this->editingId) {
            PurchaseItem::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('待采项已更新');
        } else {
            $data['status'] = PurchaseItem::STATUS_PENDING;
            PurchaseItem::create($data);
            $this->toastSuccess('待采项已创建');
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
        PurchaseItem::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('待采项已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
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
        $this->formSkuId = 0;
        $this->formQuantity = 0;
        $this->formSourceType = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => true, 'exportable' => true],
            ['key' => 'quantity', 'label' => '数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'source_type', 'label' => '来源类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return PurchaseItem::with('sku.product')->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '待采清单_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PurchaseItem::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            'SKU ID' => 'sku_id',
            '数量' => 'quantity',
            '来源类型' => 'source_type',
        ];
    }

    public function getPageIds(): array
    {
        return PurchaseItem::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = PurchaseItem::with('sku.product')->orderBy('id', 'desc');

        if ($this->search) {
            $query->whereHas('sku', function ($q) {
                $q->where('sku_code', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.purchase.purchase-item-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('待采清单');
    }
}
