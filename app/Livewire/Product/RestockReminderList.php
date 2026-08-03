<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Merchant;
use App\Models\RestockReminder;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class RestockReminderList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithPagination;
    use WithRowSelection;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = RestockReminder::class;

    public string $search = '';

    public int $filterStatus = -1;

    public int $formMerchantId = 0;

    public int $formSkuId = 0;

    public int $formThresholdQuantity = 0;

    public int $formRemindCycle = 1;

    public int $formStatus = 1;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $item = RestockReminder::findOrFail($id);
        $this->editingId = $id;
        $this->formMerchantId = $item->merchant_id;
        $this->formSkuId = $item->sku_id;
        $this->formThresholdQuantity = $item->threshold_quantity;
        $this->formRemindCycle = $item->remind_cycle;
        $this->formStatus = $item->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formMerchantId' => 'required|integer|exists:merchants,id',
            'formSkuId' => 'required|integer|exists:skus,id',
            'formThresholdQuantity' => 'required|integer|min:0',
            'formRemindCycle' => 'required|in:1,2,3',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'merchant_id' => $validated['formMerchantId'],
            'sku_id' => $validated['formSkuId'],
            'threshold_quantity' => $validated['formThresholdQuantity'],
            'remind_cycle' => $validated['formRemindCycle'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $item = RestockReminder::findOrFail($this->editingId);
            $item->update($data);
            $this->toastSuccess('补货提醒已更新');
        } else {
            RestockReminder::create($data);
            $this->toastSuccess('补货提醒已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        $item = RestockReminder::findOrFail($this->deletingId);
        $item->delete();
        $this->toastSuccess('补货提醒已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->resetPage();
        $this->clearSelection();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formSkuId = 0;
        $this->formThresholdQuantity = 0;
        $this->formRemindCycle = 1;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'threshold_quantity', 'label' => '库存阈值', 'sortable' => false, 'exportable' => true],
            ['key' => 'remind_cycle', 'label' => '提醒周期', 'sortable' => false, 'exportable' => true],
            ['key' => 'last_reminded_at', 'label' => '上次提醒', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return RestockReminder::with(['merchant', 'sku'])
            ->when($this->search, function ($q) {
                $q->whereHas('merchant', fn ($q2) => $q2->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('sku', fn ($q2) => $q2->where('sku_code', 'like', "%{$this->search}%"));
            })
            ->when($this->filterStatus >= 0, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '补货提醒_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return RestockReminder::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            'SKU ID' => 'sku_id',
            '库存阈值' => 'threshold_quantity',
            '提醒周期' => 'remind_cycle',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = RestockReminder::with(['merchant', 'sku'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('merchant', fn ($q2) => $q2->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('sku', fn ($q2) => $q2->where('sku_code', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $skus = Sku::orderBy('sku_code')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.restock-reminder-list', compact('items', 'merchants', 'skus', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('补货提醒');
    }
}
