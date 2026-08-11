<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\DeliveryNote;
use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryNoteList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = DeliveryNote::class;

    public string $search = '';
    public int $filterStatus = 0;
    public int $filterMerchantId = 0;
    public string $filterDeliveryDate = '';

    public static array $statusMap = [
        1 => '待分货', 2 => '已分货', 3 => '已签收', 4 => '已取消',
    ];

    public static array $statusColorMap = [
        1 => 'orange', 2 => 'blue', 3 => 'green', 4 => 'gray',
    ];

    public array $merchantOptions = [];

    public function mount(): void
    {
        $this->initColumnVisibility();
        $this->loadOptions();
    }

    private function loadOptions(): void
    {
        $this->merchantOptions = Merchant::enabled()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->mapWithKeys(fn($m) => [$m->id => $m->name])
            ->toArray();
    }

    // ========== 状态操作 ==========

    public function markDelivered(int $id): void
    {
        $note = DeliveryNote::findOrFail($id);
        if ($note->status !== DeliveryNote::STATUS_PENDING) {
            $this->toastError('仅待分货单据可标记已分货');
            return;
        }
        $note->update([
            'status' => DeliveryNote::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
        $this->toastSuccess('已标记为已分货');
    }

    public function markSigned(int $id): void
    {
        $note = DeliveryNote::findOrFail($id);
        if ($note->status !== DeliveryNote::STATUS_DELIVERED) {
            $this->toastError('仅已分货单据可标记已签收');
            return;
        }
        $note->update(['status' => DeliveryNote::STATUS_SIGNED]);
        $this->toastSuccess('已标记为已签收');
    }

    public function delete(): void
    {
        $note = DeliveryNote::findOrFail($this->deletingId);
        if ($note->status !== DeliveryNote::STATUS_PENDING) {
            $this->toastError('仅待分货单据可作废');
            return;
        }
        $note->update(['status' => DeliveryNote::STATUS_CANCELLED]);
        $this->toastSuccess('送货单已作废');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 0;
        $this->filterMerchantId = 0;
        $this->filterDeliveryDate = '';
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
    }

    // ========== 列配置 ==========

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'note_no', 'label' => '送货单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_name', 'label' => '商户', 'sortable' => false, 'exportable' => true],
            ['key' => 'merchant_address', 'label' => '配送地址', 'sortable' => false, 'exportable' => true],
            ['key' => 'delivery_date', 'label' => '送达日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_quantity', 'label' => '总数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'order_nos', 'label' => '关联订单', 'sortable' => false, 'exportable' => true],
            ['key' => 'product_summary', 'label' => '商品摘要', 'sortable' => false, 'exportable' => true],
            ['key' => 'delivered_at', 'label' => '分货时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['note_no', 'merchant_name', 'delivery_date', 'status', 'total_quantity'];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(DeliveryNote::with(['merchant', 'deliveryTask']))->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '送货单_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->applyFilters(DeliveryNote::query())
            ->forPage($this->getPage(), setting('per_page', 10))
            ->pluck('id')
            ->toArray();
    }

    private function applyFilters($query)
    {
        return $query
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('note_no', 'like', "%{$this->search}%")
                        ->orWhere('merchant_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus >= 1, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterMerchantId > 0, function ($q) {
                $q->where('merchant_id', $this->filterMerchantId);
            })
            ->when($this->filterDeliveryDate, function ($q) {
                $q->where('delivery_date', $this->filterDeliveryDate);
            });
    }

    private function items()
    {
        return $this->applyFilters(DeliveryNote::with(['merchant', 'deliveryTask']))
            ->orderBy('id', 'desc')
            ->paginate(setting('per_page', 10));
    }

    public function render()
    {
        $items = $this->items();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $merchantOptions = $this->merchantOptions;
        $filterMerchantId = $this->filterMerchantId;

        return view('livewire.delivery.delivery-note-list', compact(
            'items', 'allColumns', 'selectedCount', 'merchantOptions', 'filterMerchantId'
        ))
            ->layout('components.app-layout')
            ->title('送货单');
    }
}