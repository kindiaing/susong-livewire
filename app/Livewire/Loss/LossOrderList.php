<?php

namespace App\Livewire\Loss;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\LossOrder;
use App\Models\Warehouse;
use App\Services\LossOrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class LossOrderList extends Component
{
    use WithColumnVisibility, WithExcelExport, WithExcelImport, WithRowSelection;
    use WithListCrud;
    use WithPagination;
    use WithToast;

    protected string $modelClass = LossOrder::class;

    public string $search = '';

    public int $filterStatus = -1;

    public int $filterLossType = -1;

    public bool $showApproveConfirm = false;

    public ?int $approvingId = null;

    public string $approveRemark = '';

    // 关闭确认弹窗（关闭后单据不可再操作）
    public bool $showCloseConfirm = false;

    public ?int $closingId = null;

    public int $formWarehouseId = 0;

    public int $formLossType = 1;

    public string $formReason = '';

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
        $item = LossOrder::findOrFail($id);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');

            return;
        }
        $this->editingId = $id;
        $this->editingStatusLabel = $item->status_label;
        $this->editingStatusColor = 'yellow';
        $this->formWarehouseId = $item->warehouse_id;
        $this->formLossType = $item->loss_type;
        $this->formReason = $item->reason ?? '';
        $this->formRemark = $item->remark ?? '';

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formWarehouseId' => 'required|integer|exists:warehouses,id',
            'formLossType' => 'required|in:1,2,3,4,5,6',
            'formReason' => 'nullable|string|max:255',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $service = app(LossOrderService::class);

        if ($this->editingId) {
            $item = LossOrder::findOrFail($this->editingId);
            if (! $item->canEdit()) {
                $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');
                $this->showModal = false;
                $this->resetForm();

                return;
            }

            $item->update([
                'loss_type' => $validated['formLossType'],
                'reason' => $validated['formReason'],
                'remark' => $validated['formRemark'],
            ]);

            $this->toastSuccess('损耗单已更新');
            $this->showModal = false;
            $this->resetForm();
        } else {
            $lossOrder = $service->create(
                warehouseId: $validated['formWarehouseId'],
                lossType: $validated['formLossType'],
                items: [],
                reason: $validated['formReason'],
                sourceType: 'manual',
            );
            if ($validated['formRemark']) {
                $lossOrder->update(['remark' => $validated['formRemark']]);
            }

            $this->showModal = false;
            $this->resetForm();

            // 创建后跳转详情页，继续填写明细
            $this->redirect(route('loss-orders.detail', $lossOrder->id), navigate: true);
        }
    }

    public function delete(): void
    {
        $item = LossOrder::findOrFail($this->deletingId);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可删除，仅待审核状态的损耗单可删除');
            $this->showDeleteConfirm = false;
            $this->deletingId = null;

            return;
        }
        $item->items()->delete();
        $item->delete();
        $this->toastSuccess('损耗单已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function confirmApprove(int $id): void
    {
        $this->approvingId = $id;
        $this->approveRemark = '';
        $this->showApproveConfirm = true;
    }

    public function approve(): void
    {
        $item = LossOrder::findOrFail($this->approvingId);

        if ($item->approval_status !== LossOrder::APPROVAL_PENDING) {
            $this->toastError('该单不在待审核状态');
            $this->showApproveConfirm = false;

            return;
        }

        $item->update([
            'approval_status' => LossOrder::APPROVAL_APPROVED,
            'status' => LossOrder::STATUS_APPROVED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->toastSuccess('损耗单已审核通过');
        $this->showApproveConfirm = false;
        $this->approvingId = null;
    }

    public function reject(): void
    {
        $item = LossOrder::findOrFail($this->approvingId);

        if ($item->approval_status !== LossOrder::APPROVAL_PENDING) {
            $this->toastError('该单不在待审核状态');
            $this->showApproveConfirm = false;

            return;
        }

        $item->update([
            'approval_status' => LossOrder::APPROVAL_REJECTED,
            'status' => LossOrder::STATUS_CANCELLED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->toastSuccess('损耗单已拒绝');
        $this->showApproveConfirm = false;
        $this->approvingId = null;
    }

    public function execute(int $id): void
    {
        try {
            app(LossOrderService::class)->execute(LossOrder::findOrFail($id));
            $this->toastSuccess('损耗单已执行，库存已扣减');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function close(int $id): void
    {
        $item = LossOrder::findOrFail($id);

        // 采购入库差异的损耗单仅账务留痕（货未入库不扣库存），审核通过后可直接关闭
        $purchaseTraceClosable = $item->status === LossOrder::STATUS_APPROVED && $item->source_type === 'purchase_order';

        if ($item->status !== LossOrder::STATUS_EXECUTED && ! $purchaseTraceClosable) {
            $this->toastError('只有已执行的单据可关闭');
            $this->showCloseConfirm = false;
            $this->closingId = null;

            return;
        }

        $item->update([
            'status' => LossOrder::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        $this->toastSuccess('损耗单已关闭');
        $this->showCloseConfirm = false;
        $this->closingId = null;
    }

    public function confirmClose(int $id): void
    {
        $this->closingId = $id;
        $this->showCloseConfirm = true;
    }

    public function closeCloseConfirm(): void
    {
        $this->showCloseConfirm = false;
        $this->closingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterLossType = -1;
        $this->resetPage();
    }

    public function closeApproveConfirm(): void
    {
        $this->showApproveConfirm = false;
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formWarehouseId = 0;
        $this->formLossType = 1;
        $this->formReason = '';
        $this->formRemark = '';
    }

    public function getDefaultColumns(): array
    {
        return ['loss_no', 'warehouse_id', 'loss_type', 'total_amount', 'status', 'approval_status', 'reason', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'loss_no' => $row->loss_no,
                'warehouse_id' => $row->warehouse?->name ?? '',
                'loss_type' => $row->loss_type,
                'total_amount' => money_format($row->total_amount, false),
                'status' => $row->status,
                'approval_status' => $row->approval_status,
                'reason' => $row->reason ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['loss_no'];
    }

    public function getImportRequiredFields(): array
    {
        return ['仓库ID', '损耗类型'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'loss_no', 'label' => '损耗单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'warehouse_id', 'label' => '仓库', 'sortable' => true, 'exportable' => true],
            ['key' => 'loss_type', 'label' => '损耗类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '损耗金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'approval_status', 'label' => '审核状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'reason', 'label' => '原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return LossOrder::with(['warehouse'])->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '损耗管理_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return LossOrder::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '损耗单号' => 'loss_no',
            '仓库ID' => 'warehouse_id',
            '损耗类型' => 'loss_type',
            '原因' => 'reason',
        ];
    }

    public function getPageIds(): array
    {
        return LossOrder::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = LossOrder::with(['warehouse'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('loss_no', 'like', "%{$this->search}%");
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterLossType >= 0) {
            $query->where('loss_type', $this->filterLossType);
        }

        $items = $query->paginate(setting('per_page', 10));
        $warehouses = Warehouse::enabled()->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.loss.loss-order-list', compact('items', 'warehouses', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('损耗管理');
    }
}
