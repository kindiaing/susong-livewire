<?php

namespace App\Livewire\Product;

use App\Models\SkuBarcode;
use Livewire\Component;
use Livewire\WithPagination;

class SkuBarcodeList extends Component
{
    use WithPagination;

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

        return view('livewire.product.sku-barcode-list', compact('barcodes'))
            ->layout('components.app-layout')
            ->title('条码管理');
    }
}
