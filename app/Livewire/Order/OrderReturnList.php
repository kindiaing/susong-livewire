<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Services\OrderReturnService;
use Livewire\Component;
use Livewire\WithPagination;

class OrderReturnList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithListCrud;
    use WithPagination;
    use WithRowSelection;
    use WithToast;

    protected string $modelClass = OrderReturn::class;

    public string $search = '';

    public int $filterStatus = 0;

    public int $formOrderId = 0;

    public string $formReason = '';

    public string $formRemark = '';

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
            ['key' => 'total_amount', 'label' => '退货金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'refund_amount', 'label' => '退款金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => false],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '售后退货_'.now()->format('Ymd_His');
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
            '退款金额(厘)' => 'refund_amount',
            '备注' => 'remark',
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
                'total_amount' => money_format($row->total_amount, false),
                'refund_amount' => money_format($row->refund_amount, false),
                'remark' => $row->remark ?? '',
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
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');

            return;
        }
        $this->editingId = $id;
        $this->editingStatusLabel = self::$statusMap[$item->status] ?? '';
        $this->editingStatusColor = self::$statusColorMap[$item->status] ?? 'yellow';
        $this->formReason = $item->reason ?? '';
        $this->formRemark = $item->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        // 编辑模式：仅允许修改头字段（明细在详情页维护）
        if ($this->editingId) {
            $item = OrderReturn::findOrFail($this->editingId);
            if (! $item->canEdit()) {
                $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');
                $this->showModal = false;
                $this->resetForm();

                return;
            }
            $validated = $this->validate([
                'formReason' => 'nullable|string|max:255',
                'formRemark' => 'nullable|string|max:500',
            ]);
            $item->update([
                'reason' => $validated['formReason'],
                'remark' => $validated['formRemark'],
            ]);
            $this->toastSuccess('退货单已更新');
            $this->showModal = false;
            $this->resetForm();

            return;
        }

        // 创建模式：只存单头（商家自动取自订单），保存后跳转详情页整理明细
        $validated = $this->validate([
            'formOrderId' => 'required|integer|min:1|exists:orders,id',
            'formReason' => 'required|string|max:255',
            'formRemark' => 'nullable|string|max:500',
        ]);

        try {
            $order = Order::findOrFail($validated['formOrderId']);
            $return = app(OrderReturnService::class)->create($order, [], $validated['formReason']);

            if (($validated['formRemark'] ?? '') !== '') {
                $return->update(['remark' => $validated['formRemark']]);
            }
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->toastSuccess("退货单 {$return->return_no} 已创建，请添加退货明细");
        $this->showModal = false;
        $this->resetForm();

        $this->redirect(route('order-returns.detail', $return->id));
    }

    // ── 状态流转（委托服务层） ──

    public function approveReturn(int $id): void
    {
        try {
            app(OrderReturnService::class)->approve(OrderReturn::findOrFail($id));
            $this->toastSuccess('退货单已审核通过');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function rejectReturn(int $id): void
    {
        try {
            app(OrderReturnService::class)->cancel(OrderReturn::findOrFail($id), '审核拒绝');
            $this->toastSuccess('退货单已拒绝');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function startReturn(int $id): void
    {
        try {
            app(OrderReturnService::class)->markReturned(OrderReturn::findOrFail($id));
            $this->toastSuccess('退货单已确认退货');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function completeReturn(int $id): void
    {
        try {
            app(OrderReturnService::class)->refund(OrderReturn::findOrFail($id));
            $this->toastSuccess('退款已完成');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function delete(): void
    {
        $item = OrderReturn::findOrFail($this->deletingId);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可删除，仅待审核状态的退货单可删除');
            $this->showDeleteConfirm = false;
            $this->deletingId = null;

            return;
        }
        $item->items()->delete();
        $item->delete();
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

    /**
     * 批量删除：仅待审核状态可删除，其余跳过
     */
    public function batchDelete(): void
    {
        $ids = $this->selectedIds;
        if (empty($ids)) {
            $this->toastError('请先选择数据');

            return;
        }
        $deletable = OrderReturn::whereIn('id', $ids)
            ->where('status', OrderReturn::STATUS_PENDING)
            ->pluck('id')
            ->toArray();
        $skipped = count($ids) - count($deletable);
        if ($skipped > 0) {
            $this->toastError("{$skipped} 条非待审核状态退货单不可删除，已跳过");
        }
        if (! empty($deletable)) {
            foreach ($deletable as $id) {
                $return = OrderReturn::find($id);
                $return?->items()->delete();
                $return?->delete();
            }
            $this->toastSuccess('已删除 '.count($deletable).' 条退货单');
        }
        $this->clearSelection();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formOrderId = 0;
        $this->formReason = '';
        $this->formRemark = '';
    }

    private function buildQuery()
    {
        $query = OrderReturn::with(['order', 'merchant'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('return_no', 'like', "%{$this->search}%")
                    ->orWhereHas('order', function ($oq) {
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
        // 仅已签收/已锁定的订单可发起售后退货
        $orders = Order::whereIn('status', [Order::STATUS_SIGNED, Order::STATUS_LOCKED])
            ->orderBy('id', 'desc')->limit(100)->get();

        return view('livewire.order.order-return-list', [
            'items' => $items,
            'orders' => $orders,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
            'batchActions' => $this->getBatchActions(),
        ])
            ->layout('components.app-layout')
            ->title('售后退货');
    }
}
