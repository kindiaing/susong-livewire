<?php

namespace App\Livewire\Loss;

use App\Models\LossOrder;
use App\Models\Warehouse;
use App\Services\ApprovalService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LossOrderList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterStatus = -1;
    public int $filterLossType = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public bool $showApproveConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $approvingId = null;
    public string $approveRemark = '';

    public int $formWarehouseId = 0;
    public int $formLossType = 1;
    public string $formReason = '';
    public string $formRemark = '';

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $item = LossOrder::findOrFail($id);
        if ($item->status !== LossOrder::STATUS_PENDING && $item->status !== LossOrder::STATUS_APPROVED) {
            $this->dispatch('toast', message: '当前状态不可编辑', type: 'error');
            return;
        }
        $this->editingId = $id;
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

        $data = [
            'warehouse_id' => $validated['formWarehouseId'],
            'loss_type' => $validated['formLossType'],
            'reason' => $validated['formReason'],
            'remark' => $validated['formRemark'],
        ];

        if ($this->editingId) {
            $item = LossOrder::findOrFail($this->editingId);
            unset($data['warehouse_id']);
            $item->update($data);
            $this->dispatch('toast', message: '损耗单已更新', type: 'success');
        } else {
            $data['loss_no'] = 'LO' . date('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $data['total_amount'] = 0;
            $data['status'] = LossOrder::STATUS_PENDING;
            $data['approval_status'] = LossOrder::APPROVAL_PENDING;
            $data['applicant_id'] = Auth::id();
            LossOrder::create($data);
            $this->dispatch('toast', message: '损耗单已创建', type: 'success');
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
        $item = LossOrder::findOrFail($this->deletingId);
        $item->delete();
        $this->dispatch('toast', message: '损耗单已删除', type: 'success');
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
            $this->dispatch('toast', message: '该单不在待审核状态', type: 'error');
            $this->showApproveConfirm = false;
            return;
        }

        $item->update([
            'approval_status' => LossOrder::APPROVAL_APPROVED,
            'status' => LossOrder::STATUS_APPROVED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->dispatch('toast', message: '损耗单已审核通过', type: 'success');
        $this->showApproveConfirm = false;
        $this->approvingId = null;
    }

    public function reject(): void
    {
        $item = LossOrder::findOrFail($this->approvingId);

        if ($item->approval_status !== LossOrder::APPROVAL_PENDING) {
            $this->dispatch('toast', message: '该单不在待审核状态', type: 'error');
            $this->showApproveConfirm = false;
            return;
        }

        $item->update([
            'approval_status' => LossOrder::APPROVAL_REJECTED,
            'status' => LossOrder::STATUS_CANCELLED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->dispatch('toast', message: '损耗单已拒绝', type: 'success');
        $this->showApproveConfirm = false;
        $this->approvingId = null;
    }

    public function execute(int $id): void
    {
        $item = LossOrder::findOrFail($id);

        if ($item->status !== LossOrder::STATUS_APPROVED) {
            $this->dispatch('toast', message: '只有已通过的单据可执行', type: 'error');
            return;
        }

        $item->update([
            'status' => LossOrder::STATUS_EXECUTED,
            'executed_at' => now(),
        ]);

        $this->dispatch('toast', message: '损耗单已执行', type: 'success');
    }

    public function close(int $id): void
    {
        $item = LossOrder::findOrFail($id);

        if ($item->status !== LossOrder::STATUS_EXECUTED) {
            $this->dispatch('toast', message: '只有已执行的单据可关闭', type: 'error');
            return;
        }

        $item->update([
            'status' => LossOrder::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        $this->dispatch('toast', message: '损耗单已关闭', type: 'success');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterLossType = -1;
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formWarehouseId = 0;
        $this->formLossType = 1;
        $this->formReason = '';
        $this->formRemark = '';
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

        $items = $query->paginate(20);
        $warehouses = Warehouse::enabled()->orderBy('name')->get();

        return view('livewire.loss.loss-order-list', compact('items', 'warehouses'))
            ->layout('components.app-layout')
            ->title('损耗管理');
    }
}
