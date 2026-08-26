<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithToast;
use App\Models\Discrepancy;
use App\Models\Warehouse;
use App\Services\DiscrepancyService;
use Livewire\Component;

class DiscrepancyDetail extends Component
{
    use WithToast;

    public Discrepancy $discrepancy;

    // 处理弹窗
    public bool $showProcessModal = false;

    public int $processResponsibleParty = Discrepancy::PARTY_PLATFORM;

    public int $processDecision = Discrepancy::DECISION_RESTOCK;

    public string $processRemark = '';

    public ?int $processWarehouseId = null;

    // 回库弹窗
    public bool $showRestockModal = false;

    public ?int $restockWarehouseId = null;

    public string $restockRemark = '';

    // 争议弹窗
    public bool $showDisputeModal = false;

    public string $disputeRemark = '';

    public function mount(int $id): void
    {
        $this->loadDiscrepancy($id);
    }

    public function loadDiscrepancy(int $id): void
    {
        $this->discrepancy = Discrepancy::with(['order', 'orderItem.sku', 'sku', 'handler'])
            ->findOrFail($id);
    }

    public function canProcess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('delivery.discrepancy.restock')
            || $user?->can('delivery.discrepancy.refund')
            || $user?->can('delivery.discrepancy.writeoff'));
    }

    private function authorizeDecision(int $decision): void
    {
        $user = auth()->user();
        $allowed = match ($decision) {
            Discrepancy::DECISION_RESTOCK => $user?->can('delivery.discrepancy.restock'),
            Discrepancy::DECISION_REFUND, Discrepancy::DECISION_DEDUCT => $user?->can('delivery.discrepancy.refund'),
            Discrepancy::DECISION_LOSS => $user?->can('delivery.discrepancy.writeoff'),
            default => $user?->can('delivery.discrepancy.restock')
                || $user?->can('delivery.discrepancy.refund')
                || $user?->can('delivery.discrepancy.writeoff'),
        };

        abort_unless((bool) $allowed, 403);
    }

    // ========== 处理 ==========

    public function openProcess(): void
    {
        if (! in_array($this->discrepancy->status, [Discrepancy::STATUS_PENDING, Discrepancy::STATUS_PROCESSING, Discrepancy::STATUS_DISPUTED])) {
            $this->toastError('当前状态不可处理');

            return;
        }
        $this->processResponsibleParty = $this->discrepancy->responsible_party ?? Discrepancy::PARTY_PLATFORM;
        $this->processDecision = $this->discrepancy->decision ?? Discrepancy::DECISION_RESTOCK;
        $this->processRemark = $this->discrepancy->remark ?? '';
        $this->processWarehouseId = Warehouse::orderBy('id')->value('id');
        $this->showProcessModal = true;
    }

    public function process(): void
    {
        $this->authorizeDecision($this->processDecision);

        try {
            app(DiscrepancyService::class)->process(
                discrepancy: $this->discrepancy,
                responsibleParty: $this->processResponsibleParty,
                decision: $this->processDecision,
                remark: trim($this->processRemark) ?: null,
                warehouseId: $this->processWarehouseId,
            );
            $this->toastSuccess(match ($this->processDecision) {
                Discrepancy::DECISION_LOSS => setting('discrepancy_auto_create_loss', true)
                    ? '已处理：报损损耗单已生成，待审核'
                    : '已处理：报损决策已登记（自动生成损耗单已关闭，请手动建单）',
                Discrepancy::DECISION_REFUND, Discrepancy::DECISION_DEDUCT => '已处理：决策已登记，金额调整待财务二期执行',
                Discrepancy::DECISION_IGNORE => '差异单已关闭（不计）',
                default => '已处理：补货决策已登记',
            });
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->closeProcessModal();
        $this->loadDiscrepancy($this->discrepancy->id);
    }

    public function closeProcessModal(): void
    {
        $this->showProcessModal = false;
        $this->processRemark = '';
    }

    // ========== 回库 ==========

    public function openRestock(): void
    {
        abort_unless(auth()->user()?->can('delivery.discrepancy.restock'), 403);
        $this->restockWarehouseId = Warehouse::orderBy('id')->value('id');
        $this->restockRemark = '';
        $this->showRestockModal = true;
    }

    public function restock(): void
    {
        abort_unless(auth()->user()?->can('delivery.discrepancy.restock'), 403);

        try {
            app(DiscrepancyService::class)->restockToWarehouse(
                discrepancy: $this->discrepancy,
                warehouseId: (int) $this->restockWarehouseId,
                remark: trim($this->restockRemark) ?: null,
            );
            $this->toastSuccess('已回库，库存已回补并留痕');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->closeRestockModal();
        $this->loadDiscrepancy($this->discrepancy->id);
    }

    public function closeRestockModal(): void
    {
        $this->showRestockModal = false;
        $this->restockRemark = '';
    }

    // ========== 状态流转 ==========

    public function markProcessing(): void
    {
        abort_unless($this->canProcess(), 403);

        try {
            app(DiscrepancyService::class)->markProcessing($this->discrepancy);
            $this->toastSuccess('已标记为处理中');
            $this->loadDiscrepancy($this->discrepancy->id);
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function openDispute(): void
    {
        if (! in_array($this->discrepancy->status, [Discrepancy::STATUS_PENDING, Discrepancy::STATUS_PROCESSING])) {
            $this->toastError('当前状态不可转入争议');

            return;
        }
        $this->disputeRemark = '';
        $this->showDisputeModal = true;
    }

    public function markDisputed(): void
    {
        abort_unless($this->canProcess(), 403);

        try {
            app(DiscrepancyService::class)->markDisputed($this->discrepancy, trim($this->disputeRemark) ?: null);
            $this->toastSuccess('已标记为争议中');
            $this->showDisputeModal = false;
            $this->loadDiscrepancy($this->discrepancy->id);
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeDisputeModal(): void
    {
        $this->showDisputeModal = false;
        $this->disputeRemark = '';
    }

    public function render()
    {
        $statusMap = Discrepancy::statusMap();
        $stageMap = Discrepancy::stageMap();
        $typeMap = Discrepancy::typeMap();
        $partyMap = [
            Discrepancy::PARTY_SUPPLIER => '供应商',
            Discrepancy::PARTY_PLATFORM => '平台',
            Discrepancy::PARTY_DRIVER => '司机',
            Discrepancy::PARTY_MERCHANT => '商家',
        ];
        $decisionMap = [
            Discrepancy::DECISION_RESTOCK => '补货',
            Discrepancy::DECISION_REFUND => '退款',
            Discrepancy::DECISION_DEDUCT => '扣款',
            Discrepancy::DECISION_LOSS => '报损',
            Discrepancy::DECISION_IGNORE => '不计',
        ];
        $warehouses = Warehouse::orderBy('id')->get(['id', 'name']);

        return view('livewire.delivery.discrepancy-detail', compact(
            'statusMap', 'stageMap', 'typeMap', 'partyMap', 'decisionMap', 'warehouses'
        ))
            ->layout('components.app-layout')
            ->title("差异单 {$this->discrepancy->discrepancy_no}");
    }
}