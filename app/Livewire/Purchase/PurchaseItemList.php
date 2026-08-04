<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseItem;
use App\Models\Sku;
use App\Models\Supplier;
use App\Services\PurchaseService;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseItemList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = PurchaseItem::class;

    public string $search = '';
    public int $filterStatus = -1;

    public int $formSkuId = 0;
    public int $formSupplierId = 0;
    public int $formQuantity = 0;
    public int $formSourceType = 1;
    public bool $showGenerateConfirm = false;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $item = PurchaseItem::findOrFail($id);
        $this->editingId = $id;
        $this->formSkuId = $item->sku_id;
        $this->formSupplierId = $item->supplier_id ?? 0;
        $this->formQuantity = $item->quantity;
        $this->formSourceType = $item->source_type;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSkuId' => 'required|integer|min:1|exists:skus,id',
            'formSupplierId' => 'nullable|integer|min:0|exists:suppliers,id',
            'formQuantity' => 'required|integer|min:1',
            'formSourceType' => 'required|in:1,2',
        ]);

        $data = [
            'sku_id' => $validated['formSkuId'],
            'supplier_id' => $validated['formSupplierId'] > 0 ? $validated['formSupplierId'] : null,
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

    /**
     * 批量生成采购单
     */
    public function confirmGenerateOrders(): void
    {
        $selectedIds = $this->getSelectedIds();
        if (empty($selectedIds)) {
            $this->toastError('请先勾选待采项');
            return;
        }
        $this->showGenerateConfirm = true;
    }

    public function generateOrders(): void
    {
        $selectedIds = $this->getSelectedIds();
        try {
            $service = app(PurchaseService::class);
            $orderIds = $service->createFromItems($selectedIds);
            $this->showGenerateConfirm = false;
            $this->clearSelection();
            $this->toastSuccess('已生成 ' . count($orderIds) . ' 个采购单');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeGenerateConfirm(): void
    {
        $this->showGenerateConfirm = false;
        $this->resetErrorBag();
    }

    /**
     * 获取选中ID
     */
    private function getSelectedIds(): array
    {
        return array_keys(array_filter($this->selectedRows ?? []));
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formSkuId = 0;
        $this->formSupplierId = 0;
        $this->formQuantity = 0;
        $this->formSourceType = 1;
    }

    public function getDefaultColumns(): array
    {
        return ['sku_id', 'supplier_id', 'quantity', 'source_type', 'status'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'sku_id' => $row->sku?->sku_code ?? '',
                'quantity' => $row->quantity,
                'source_type' => $row->source_type,
                'status' => $row->status,
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['SKU ID', '数量'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => true, 'exportable' => true],
            ['key' => 'supplier_id', 'label' => '供应商', 'sortable' => true, 'exportable' => true],
            ['key' => 'quantity', 'label' => '数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'expected_price', 'label' => '预估成本价', 'sortable' => true, 'exportable' => true],
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

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        $skuOptions = Sku::with('product')->orderBy('sku_code')->get()->map(fn($s) => ['value' => $s->id, 'label' => $s->sku_code . ' - ' . ($s->product?->name ?? '')])->toArray();
        $supplierOptions = Supplier::where('status', 1)->orderBy('name')->get()->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray();

        return view('livewire.purchase.purchase-item-list', compact('items', 'allColumns', 'selectedCount', 'skuOptions', 'supplierOptions'))
            ->layout('components.app-layout')
            ->title('待采清单');
    }
}
