<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Models\SkuBarcode;
use Livewire\Component;
use Livewire\WithPagination;

class SkuBarcodeList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;

    protected string $modelClass = SkuBarcode::class;

    public string $search = '';
    public int $filterBarcodeType = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formSkuId = 0;
    public int $formSupplierId = 0;
    public int $formBarcodeType = 1;
    public string $formBarcodeCode = '';
    public int $formIsDefault = 0;
    public int $formIsEnabled = 1;
    public string $formRemark = '';

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
        $barcode = SkuBarcode::findOrFail($id);
        $this->editingId = $id;
        $this->formSkuId = $barcode->sku_id;
        $this->formSupplierId = $barcode->supplier_id ?? 0;
        $this->formBarcodeType = $barcode->barcode_type;
        $this->formBarcodeCode = $barcode->barcode_code;
        $this->formIsDefault = $barcode->is_default;
        $this->formIsEnabled = $barcode->is_enabled;
        $this->formRemark = $barcode->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSkuId' => 'required|integer|min:1',
            'formSupplierId' => 'nullable|integer',
            'formBarcodeType' => 'required|in:1,2,3,4',
            'formBarcodeCode' => 'required|string|max:50',
            'formIsDefault' => 'required|in:0,1',
            'formIsEnabled' => 'required|in:0,1',
            'formRemark' => 'nullable|string|max:255',
        ]);

        $data = [
            'sku_id' => $validated['formSkuId'],
            'supplier_id' => $validated['formSupplierId'] ?: null,
            'barcode_type' => $validated['formBarcodeType'],
            'barcode_code' => $validated['formBarcodeCode'],
            'is_default' => $validated['formIsDefault'],
            'is_enabled' => $validated['formIsEnabled'],
            'remark' => $validated['formRemark'] ?: null,
        ];

        if ($this->editingId) {
            SkuBarcode::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '条码已更新', type: 'success');
        } else {
            SkuBarcode::create($data);
            $this->dispatch('toast', message: '条码已创建', type: 'success');
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
        SkuBarcode::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '条码已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterBarcodeType = -1;
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
        $this->formSkuId = 0;
        $this->formSupplierId = 0;
        $this->formBarcodeType = 1;
        $this->formBarcodeCode = '';
        $this->formIsDefault = 0;
        $this->formIsEnabled = 1;
        $this->formRemark = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'barcode', 'label' => '条码', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return SkuBarcode::with(['sku', 'supplier'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('barcode_code', 'like', "%{$this->search}%")
                        ->orWhereHas('sku', function ($sq) {
                            $sq->where('sku_code', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterBarcodeType > 0, function ($q) {
                $q->where('barcode_type', $this->filterBarcodeType);
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '条码_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return SkuBarcode::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            'SKU ID' => 'sku_id',
            '条码类型' => 'barcode_type',
            '条码值' => 'barcode_code',
            '是否默认' => 'is_default',
            '是否启用' => 'is_enabled',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = SkuBarcode::with(['sku', 'supplier'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('barcode_code', 'like', "%{$this->search}%")
                    ->orWhereHas('sku', function ($sq) {
                        $sq->where('sku_code', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->filterBarcodeType > 0) {
            $query->where('barcode_type', $this->filterBarcodeType);
        }

        $barcodes = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.sku-barcode-list', compact('barcodes', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('条码管理');
    }
}
