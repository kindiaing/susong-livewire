<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Discrepancy;
use App\Models\Warehouse;
use App\Services\DiscrepancyService;
use Livewire\Component;
use Livewire\WithPagination;

class DiscrepancyList extends Component
{
    use WithColumnVisibility, WithExcelExport, WithExcelImport, WithRowSelection;
    use WithListCrud;
    use WithPagination;
    use WithToast;

    protected string $modelClass = Discrepancy::class;

    public string $search = '';

    public string $stageFilter = '';

    public string $statusFilter = '';

    // 处理弹窗
    public bool $showProcessModal = false;

    public ?int $processingId = null;

    public ?Discrepancy $processing = null;

    public int $processResponsibleParty = Discrepancy::PARTY_PLATFORM;

    public int $processDecision = Discrepancy::DECISION_RESTOCK;

    public string $processRemark = '';

    public ?int $processWarehouseId = null;

    // 回库弹窗
    public bool $showRestockModal = false;

    public ?int $restockId = null;

    public ?int $restockWarehouseId = null;

    public string $restockRemark = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    // ========== 处理台操作 ==========

    public function openProcess(int $id): void
    {
        $this->processing = Discrepancy::with(['order', 'orderItem.sku'])->findOrFail($id);
        $this->processingId = $id;
        $this->processResponsibleParty = $this->processing->responsible_party ?? Discrepancy::PARTY_PLATFORM;
        $this->processDecision = $this->processing->decision ?? Discrepancy::DECISION_RESTOCK;
        $this->processRemark = $this->processing->remark ?? '';
        $this->processWarehouseId = Warehouse::orderBy('id')->value('id');
        $this->showProcessModal = true;
    }

    public function process(): void
    {
        $this->authorizeDecision($this->processDecision);

        try {
            app(DiscrepancyService::class)->process(
                discrepancy: Discrepancy::findOrFail($this->processingId),
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
    }

    public function closeProcessModal(): void
    {
        $this->showProcessModal = false;
        $this->processingId = null;
        $this->processing = null;
        $this->processRemark = '';
    }

    public function openRestock(int $id): void
    {
        abort_unless(auth()->user()?->can('delivery.discrepancy.restock'), 403);
        $this->restockId = $id;
        $this->restockWarehouseId = Warehouse::orderBy('id')->value('id');
        $this->restockRemark = '';
        $this->showRestockModal = true;
    }

    public function restock(): void
    {
        abort_unless(auth()->user()?->can('delivery.discrepancy.restock'), 403);

        try {
            app(DiscrepancyService::class)->restockToWarehouse(
                discrepancy: Discrepancy::findOrFail($this->restockId),
                warehouseId: (int) $this->restockWarehouseId,
                remark: trim($this->restockRemark) ?: null,
            );
            $this->toastSuccess('已回库，库存已回补并留痕');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->showRestockModal = false;
        $this->restockId = null;
    }

    /**
     * 决策按钮级权限：补货→restock，退款/扣款→refund，报损→writeoff，不计→任一决策权限
     */
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

    public function canProcess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('delivery.discrepancy.restock')
            || $user?->can('delivery.discrepancy.refund')
            || $user?->can('delivery.discrepancy.writeoff'));
    }

    // ========== 列表 ==========

    public function delete(): void
    {
        Discrepancy::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'discrepancy_no', 'label' => '差异编号', 'sortable' => true, 'exportable' => true],
            ['key' => 'stage', 'label' => '环节', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '差异类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'quantity_diff', 'label' => '数量差异', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount_diff', 'label' => '差异金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'responsible_party', 'label' => '责任方', 'sortable' => true, 'exportable' => true],
            ['key' => 'decision', 'label' => '处理决策', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'reason', 'label' => '原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['discrepancy_no', 'stage', 'type', 'quantity_diff', 'amount_diff', 'responsible_party', 'decision', 'status', 'reason', 'created_at'];
    }

    public function getExportQuery()
    {
        return Discrepancy::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '差异处理_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Discrepancy::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '差异编号' => 'discrepancy_no',
            '差异类型' => 'type',
            '原因' => 'reason',
        ];
    }

    public function getPageIds(): array
    {
        return Discrepancy::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Discrepancy::with(['orderItem.sku'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('discrepancy_no', 'like', "%{$this->search}%");
        }
        if ($this->stageFilter !== '') {
            $query->where('stage', (int) $this->stageFilter);
        }
        if ($this->statusFilter !== '') {
            $query->where('status', (int) $this->statusFilter);
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $warehouses = Warehouse::orderBy('id')->get(['id', 'name']);

        return view('livewire.delivery.discrepancy-list', compact('items', 'allColumns', 'selectedCount', 'warehouses'))
            ->layout('components.app-layout')
            ->title('差异处理');
    }
}
