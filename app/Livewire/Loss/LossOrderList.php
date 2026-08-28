<?php

namespace App\Livewire\Loss;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\LossOrder;
use App\Models\LossOrderItem;
use App\Models\Sku;
use App\Models\Supplier;
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

    public int $formWarehouseId = 0;

    public int $formLossType = 1;

    public string $formReason = '';

    public string $formRemark = '';

    /** @var array<int, array{sku_id: int, cost_price_yuan: string, quantity: string, responsible_party: int, supplier_id: int, reason: string}> */
    public array $formItems = [];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->addItemRow();
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

        // 加载已有明细到表单
        $this->formItems = [];
        foreach ($item->items as $detail) {
            $this->formItems[] = [
                'sku_id' => $detail->sku_id,
                'cost_price_yuan' => number_format($detail->cost_price / 1000, 2),
                'quantity' => (string) $detail->quantity,
                'responsible_party' => $detail->responsible_party,
                'supplier_id' => $detail->supplier_id ?? 0,
                'reason' => $detail->reason ?? '',
            ];
        }
        if (empty($this->formItems)) {
            $this->addItemRow();
        }

        $this->showModal = true;
    }

    public function addItemRow(): void
    {
        $this->formItems[] = [
            'sku_id' => 0,
            'cost_price_yuan' => '',
            'quantity' => '',
            'responsible_party' => LossOrderItem::PARTY_PLATFORM,
            'supplier_id' => 0,
            'reason' => '',
        ];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->formItems[$index]);
        $this->formItems = array_values($this->formItems);
    }

    /**
     * SKU 选择时自动带出成本价（cost_price 为 0 时 fallback 到 purchase_price）
     */
    public function updatedFormItems($value, $key): void
    {
        if (! str_ends_with($key, '.sku_id')) {
            return;
        }

        $index = (int) explode('.', $key)[0];
        $skuId = (int) $value;

        if ($skuId <= 0) {
            return;
        }

        $sku = Sku::find($skuId);
        if ($sku) {
            $costPrice = $sku->cost_price > 0 ? $sku->cost_price : $sku->purchase_price;
            $this->formItems[$index]['cost_price_yuan'] = number_format($costPrice / 1000, 2);
        }
    }

    /**
     * 计算表单明细总金额（厘）
     */
    public function getFormTotalAmountCentsProperty(): int
    {
        $total = 0;
        foreach ($this->formItems as $row) {
            $qty = (int) ($row['quantity'] ?? 0);
            $priceCents = (int) round((float) ($row['cost_price_yuan'] ?? 0) * 1000);
            if ($qty > 0 && $priceCents > 0) {
                $total += $qty * $priceCents;
            }
        }

        return $total;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formWarehouseId' => 'required|integer|exists:warehouses,id',
            'formLossType' => 'required|in:1,2,3,4,5,6',
            'formReason' => 'nullable|string|max:255',
            'formRemark' => 'nullable|string|max:500',
            'formItems' => 'required|array|min:1',
            'formItems.*.sku_id' => 'required|integer|min:1',
            'formItems.*.cost_price_yuan' => 'required|numeric|min:0',
            'formItems.*.quantity' => 'required|integer|min:1',
            'formItems.*.responsible_party' => 'required|in:1,2',
            'formItems.*.supplier_id' => 'nullable|integer|min:0',
            'formItems.*.reason' => 'nullable|string|max:255',
        ], [
            'formItems.min' => '至少添加一条损耗明细',
            'formItems.*.sku_id.min' => '请选择 SKU',
            'formItems.*.quantity.min' => '数量必须大于 0',
            'formItems.*.cost_price_yuan.required' => '成本价不能为空',
        ]);

        // 组装明细数据
        $items = [];
        foreach ($validated['formItems'] as $row) {
            $costPriceCents = (int) round((float) $row['cost_price_yuan'] * 1000);
            $quantity = (int) $row['quantity'];
            $items[] = [
                'sku_id' => $row['sku_id'],
                'cost_price' => $costPriceCents,
                'quantity' => $quantity,
                'loss_amount' => $costPriceCents * $quantity,
                'responsible_party' => $row['responsible_party'],
                'supplier_id' => $row['responsible_party'] === LossOrderItem::PARTY_SUPPLIER ? ($row['supplier_id'] ?: null) : null,
                'reason' => $row['reason'] ?: null,
            ];
        }

        $service = app(LossOrderService::class);

        if ($this->editingId) {
            $item = LossOrder::findOrFail($this->editingId);
            if (! $item->canEdit()) {
                $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');
                $this->showModal = false;
                $this->resetForm();

                return;
            }

            DB::transaction(function () use ($item, $validated, $items) {
                $item->update([
                    'loss_type' => $validated['formLossType'],
                    'reason' => $validated['formReason'],
                    'remark' => $validated['formRemark'],
                ]);
                // 删除旧明细重新创建
                $item->items()->delete();
                $totalAmount = 0;
                foreach ($items as $data) {
                    $data['loss_order_id'] = $item->id;
                    $data['loss_type'] = $validated['formLossType'];
                    LossOrderItem::create($data);
                    $totalAmount += $data['loss_amount'];
                }
                $item->update(['total_amount' => $totalAmount]);
            });

            $this->toastSuccess('损耗单已更新');
        } else {
            $lossOrder = $service->create(
                warehouseId: $validated['formWarehouseId'],
                lossType: $validated['formLossType'],
                items: $items,
                reason: $validated['formReason'],
                sourceType: 'manual',
            );
            // 更新备注（LossOrderService::create 不支持 remark 参数）
            if ($validated['formRemark']) {
                $lossOrder->update(['remark' => $validated['formRemark']]);
            }
            $this->toastSuccess('损耗单已创建');
        }

        $this->showModal = false;
        $this->resetForm();
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

            return;
        }

        $item->update([
            'status' => LossOrder::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        $this->toastSuccess('损耗单已关闭');
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
        $this->formItems = [];
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
        $suppliers = Supplier::enabled()->get();
        $skus = Sku::with('product')->where('status', 1)->orderBy('id', 'desc')->limit(200)->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $formTotalAmount = $this->formTotalAmountCents;

        return view('livewire.loss.loss-order-list', compact('items', 'warehouses', 'suppliers', 'skus', 'allColumns', 'selectedCount', 'formTotalAmount'))
            ->layout('components.app-layout')
            ->title('损耗管理');
    }
}
