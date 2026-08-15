<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Merchant;
use App\Models\OrderReturn;
use Livewire\Component;
use Livewire\WithPagination;

class OrderReturnList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = OrderReturn::class;

    public string $search = '';
    public int $filterStatus = 0;

    public int $formOrderId = 0;
    public int $formMerchantId = 0;
    public string $formReason = '';
    public string $formNote = '';

    public static array $statusMap = [
        1 => '待审核', 2 => '已审核', 3 => '已退货',
        4 => '退款完成', 9 => '已作废',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'green', 3 => 'blue',
        4 => 'gray', 9 => 'red',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order_id', 'label' => '订单号', 'sortable' => false, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'reason', 'label' => '退货原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'refund_amount', 'label' => '退款金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => false],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '售后退货_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return OrderReturn::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '订单ID' => 'order_id',
            '商家ID' => 'merchant_id',
            '退货原因' => 'reason',
            '退款金额(分)' => 'refund_amount',
            '备注' => 'note',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['order_id'];
    }

    public function getExportRowCallback(): callable
    {
        return function (OrderReturn $row) {
            return [
                'id' => $row->id,
                'order_id' => $row->order?->order_no ?? '',
                'merchant_id' => $row->merchant?->name ?? '',
                'reason' => $row->reason ?? '',
                'status' => self::$statusMap[$row->status] ?? '',
                'refund_amount' => money_format($row->refund_amount, false),
                'note' => $row->note ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getPageIds(): array
    {
        return $this->buildQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function openEditModal(int $id): void
    {
        $item = OrderReturn::findOrFail($id);
        $this->editingId = $id;
        $this->formReason = $item->reason ?? '';
        $this->formNote = $item->note ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $validated = $this->validate([
                'formReason' => 'nullable|string|max:255',
                'formNote' => 'nullable|string|max:500',
            ]);
            OrderReturn::findOrFail($this->editingId)->update([
                'reason' => $validated['formReason'],
                'note' => $validated['formNote'],
            ]);
            $this->toastSuccess('退货单已更新');
        } else {
            $validated = $this->validate([
                'formOrderId' => 'required|integer|exists:orders,id',
                'formMerchantId' => 'required|integer|exists:merchants,id',
                'formReason' => 'required|string|max:255',
            ]);
            OrderReturn::create([
                'return_no' => 'RT' . date('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'order_id' => $validated['formOrderId'],
                'merchant_id' => $validated['formMerchantId'],
                'reason' => $validated['formReason'],
                'status' => 1,
                'refund_amount' => 0,
            ]);
            $this->toastSuccess('退货单已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function approveReturn(int $id): void
    {
        $item = OrderReturn::findOrFail($id);
        if ($item->status != 1) {
            $this->toastError('只有待审核退货单可审核通过');
            return;
        }
        $item->update(['status' => 2]);
        $this->toastSuccess('退货单已审核通过');
    }

    public function rejectReturn(int $id): void
    {
        $item = OrderReturn::findOrFail($id);
        if ($item->status != 1) {
            $this->toastError('只有待审核退货单可拒绝');
            return;
        }
        $item->update(['status' => 9]);
        $this->toastSuccess('退货单已拒绝');
    }

    public function startReturn(int $id): void
    {
        $item = OrderReturn::findOrFail($id);
        if ($item->status != 2) {
            $this->toastError('只有已审核的退货单可开始退货');
            return;
        }
        $item->update(['status' => 3]);
        $this->toastSuccess('退货单已开始退货');
    }

    public function completeReturn(int $id): void
    {
        $item = OrderReturn::findOrFail($id);
        if ($item->status != 3) {
            $this->toastError('只有已退货的单据可完成退款');
            return;
        }
        $item->update(['status' => 4]);
        $this->toastSuccess('退货单已完成');
    }

    public function delete(): void
    {
        OrderReturn::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('退货单已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->resetPage();
    }

    public function getBatchActions(): array
    {
        return [
            ['label' => '批量删除', 'method' => 'batchDelete', 'color' => 'bg-red-600 hover:bg-red-700'],
        ];
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formOrderId = 0;
        $this->formMerchantId = 0;
        $this->formReason = '';
        $this->formNote = '';
    }

    private function buildQuery()
    {
        $query = OrderReturn::with(['order', 'merchant'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('order', function ($oq) {
                    $oq->where('order_no', 'like', "%{$this->search}%");
                })->orWhereHas('merchant', function ($mq) {
                    $mq->where('name', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->filterStatus >= 1) {
            $query->where('status', $this->filterStatus);
        }

        return $query;
    }

    public function render()
    {
        $items = $this->buildQuery()->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();

        return view('livewire.order.order-return-list', [
            'items' => $items,
            'merchants' => $merchants,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
            'batchActions' => $this->getBatchActions(),
        ])
            ->layout('components.app-layout')
            ->title('售后退货');
    }
}
