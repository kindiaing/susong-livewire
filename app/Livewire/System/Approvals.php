<?php

namespace App\Livewire\System;

use App\Models\Approval;
use App\Services\ApprovalService;
use App\Services\DiscrepancyService;
use App\Services\SupplierSettlementService;
use Livewire\Component;
use Livewire\WithPagination;

class Approvals extends Component
{
    use WithPagination;

    public string $activeTab = 'pending'; // pending | approved | rejected | all
    public string $filterType = '';
    public int $selectedId = 0;
    public string $reviewRemark = '';

    public function mount(): void
    {
        // 默认显示待审核
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function showDetail(int $id): void
    {
        $this->selectedId = $id;
        $this->reviewRemark = '';
    }

    public function closeDetail(): void
    {
        $this->selectedId = 0;
        $this->reviewRemark = '';
    }

    public function approve(int $id): void
    {
        $approval = Approval::find($id);
        if (!$approval) return;

        $result = ApprovalService::approve($approval, $this->reviewRemark);

        if ($result) {
            // 业务联动分发：审核通过后调用对应 Service 执行业务逻辑
            $this->dispatchApprovalBusiness($approval, true);

            session()->flash('success', "审批「{$approval->applicant_name}」的申请已通过");
        } else {
            session()->flash('error', '操作失败，审批状态可能已变更');
        }

        $this->selectedId = 0;
        $this->reviewRemark = '';
    }

    public function reject(int $id): void
    {
        $approval = Approval::find($id);
        if (!$approval) return;

        if (empty($this->reviewRemark)) {
            session()->flash('error', '拒绝审批时必须填写原因');
            return;
        }

        $result = ApprovalService::reject($approval, $this->reviewRemark);

        if ($result) {
            // 业务联动分发：审核拒绝后调用对应 Service 回退业务状态
            $this->dispatchApprovalBusiness($approval, false);

            session()->flash('success', "审批「{$approval->applicant_name}」的申请已拒绝");
        } else {
            session()->flash('error', '操作失败，审批状态可能已变更');
        }

        $this->selectedId = 0;
        $this->reviewRemark = '';
    }

    public function withdraw(int $id): void
    {
        $approval = Approval::find($id);
        if (!$approval) return;

        $result = ApprovalService::withdraw($approval);

        if ($result) {
            session()->flash('success', '审批申请已撤回');
        } else {
            session()->flash('error', '撤回失败，只能撤回自己的待审核申请');
        }

        $this->selectedId = 0;
        $this->reviewRemark = '';
    }

    /**
     * 审批后业务联动分发
     * 根据审批类型调用对应 Service 的方法执行/回退业务逻辑
     */
    private function dispatchApprovalBusiness(Approval $approval, bool $approved): void
    {
        try {
            $discrepancyService = app(DiscrepancyService::class);

            match ($approval->approval_type) {
                'diff_refund_deduct' => $approved
                    ? $discrepancyService->applyRefundDeductApproval($approval->target_id)
                    : $discrepancyService->rejectRefundDeductApproval($approval->target_id),
                'diff_restock' => $approved
                    ? $discrepancyService->applyRestockApproval($approval->target_id, $approval->after_data)
                    : $discrepancyService->rejectRestockApproval($approval->target_id),
                'supplier_payment' => $approved
                    ? app(SupplierSettlementService::class)->applyPayment($approval->target_id)
                    : app(SupplierSettlementService::class)->rejectPayment($approval->target_id),
                default => null,
            };
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('审批后业务联动失败', [
                'approval_id' => $approval->id,
                'approval_type' => $approval->approval_type,
                'approved' => $approved,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $query = Approval::with(['applicant', 'reviewer', 'typeConfig'])
            ->orderBy('created_at', 'desc');

        // 按 Tab 筛选
        match ($this->activeTab) {
            'pending' => $query->where('status', Approval::STATUS_PENDING),
            'approved' => $query->where('status', Approval::STATUS_APPROVED),
            'rejected' => $query->where('status', Approval::STATUS_REJECTED),
            'all' => null,
            default => null,
        };

        // 按类型筛选
        if ($this->filterType) {
            $query->where('approval_type', $this->filterType);
        }

        $approvals = $query->paginate(setting('per_page', 10));

        // 获取审批类型列表（用于筛选）
        $approvalTypes = Approval::select('approval_type')
            ->distinct()
            ->pluck('approval_type');

        // 统计
        $pendingCount = Approval::where('status', Approval::STATUS_PENDING)->count();

        // 选中的详情
        $detailApproval = $this->selectedId ? Approval::with(['applicant', 'reviewer', 'typeConfig'])->find($this->selectedId) : null;

        return view('livewire.system.approvals', compact('approvals', 'approvalTypes', 'pendingCount', 'detailApproval'))
            ->layout('components.app-layout')
            ->title('审批列表');
    }
}
