<?php

namespace App\Livewire\Purchase;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Sku;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseReturnService;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseReturnList extends Component
{
    use WithColumnVisibility, WithExcelExport, WithExcelImport, WithRowSelection;
    use WithListCrud;
    use WithPagination;
    use WithToast;

    protected string $modelClass = PurchaseReturn::class;

    public string $search = '';

    public int $filterStatus = -1;

    // 表单：退货类型（1普通/2关联）
    public int $formReturnType = PurchaseReturn::TYPE_LINKED;

    public int $formPurchaseOrderId = 0;

    public int $formSupplierId = 0;

    public int $formWarehouseId = 0;

    public string $formReason = '';

    public string $formRemark = '';

    /**
     * 退货明细行
     * 关联模式：[{purchase_order_item_id, sku_id, sku_name, max_quantity, quantity, price(厘), reason}]
     * 普通模式：[{sku_id, quantity, price_yuan(元), reason}]
     */
    public array $formItems = [];

    // 明细导入文件（普通模式）
    public $itemsImportFile = null;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $item = PurchaseReturn::findOrFail($id);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');

            return;
        }
        $this->editingId = $id;
        $this->editingStatusLabel = $item->status_label;
        $this->editingStatusColor = 'yellow';
        $this->formReturnType = $item->return_type ?? PurchaseReturn::TYPE_LINKED;
        $this->formPurchaseOrderId = $item->purchase_order_id ?? 0;
        $this->formSupplierId = $item->supplier_id;
        $this->formWarehouseId = $item->warehouse_id;
        $this->formReason = $item->reason ?? '';
        $this->formRemark = $item->remark ?? '';
        $this->formItems = [];
        $this->showModal = true;
    }

    /**
     * 切换退货类型：清空已选采购单与明细行
     */
    public function updatedFormReturnType(): void
    {
        $this->formPurchaseOrderId = 0;
        $this->formItems = [];
        $this->itemsImportFile = null;
    }

    /**
     * 关联模式：选中采购单后拉取明细行（上限=实际入库数量）
     */
    public function updatedFormPurchaseOrderId($value): void
    {
        $this->formItems = [];
        $order = PurchaseOrder::with(['items.sku.product'])->find((int) $value);
        if (! $order) {
            return;
        }

        // 自动带出供应商与入库仓库
        $this->formSupplierId = (int) $order->supplier_id;
        $this->formWarehouseId = (int) ($order->warehouse_id ?? 0);

        foreach ($order->items as $oi) {
            if ($oi->actual_quantity <= 0) {
                continue;
            }
            $this->formItems[] = [
                'purchase_order_item_id' => $oi->id,
                'sku_id' => $oi->sku_id,
                'sku_name' => ($oi->sku?->product?->name ?? 'SKU').'（'.$oi->sku?->sku_code.'）',
                'max_quantity' => (int) $oi->actual_quantity,
                'quantity' => (int) $oi->actual_quantity,
                'price' => (int) ($oi->actual_price ?: $oi->price),
                'reason' => '',
            ];
        }
    }

    /**
     * 普通模式：新增一行自定义明细
     */
    public function addItemRow(): void
    {
        $this->formItems[] = [
            'sku_id' => 0,
            'quantity' => '',
            'price_yuan' => '',
            'reason' => '',
        ];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->formItems[$index]);
        $this->formItems = array_values($this->formItems);
    }

    /**
     * 普通模式：导入明细（列：SKU编码 | 数量 | 单价(元) | 原因）
     */
    public function importItems(): void
    {
        $this->validate([
            'itemsImportFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $rows = Excel::toArray(null, $this->itemsImportFile)[0] ?? [];
        $failures = [];
        $added = 0;

        foreach (array_slice($rows, 1) as $row) {
            $code = trim((string) ($row[0] ?? ''));
            if ($code === '') {
                continue;
            }
            $sku = Sku::where('sku_code', $code)->first();
            if (! $sku) {
                $failures[] = $code;

                continue;
            }
            $this->formItems[] = [
                'sku_id' => $sku->id,
                'quantity' => (string) (int) ($row[1] ?? 0),
                'price_yuan' => (string) ($row[2] ?? ''),
                'reason' => trim((string) ($row[3] ?? '')),
            ];
            $added++;
        }

        $this->itemsImportFile = null;

        if ($failures !== []) {
            $this->toastWarning("已导入 {$added} 行，未识别 SKU 编码：".implode('、', $failures));
        } else {
            $this->toastSuccess("已导入 {$added} 行明细");
        }
    }

    /**
     * 普通模式：下载明细导入模板
     */
    public function exportItemsTemplate()
    {
        return response()->streamDownload(function () {
            echo "\xEF\xBB\xBF"."SKU编码,数量,单价(元),原因\n";
            echo "示例SKU-001,10,5.5,破损退货\n";
        }, '采购退货明细导入模板.csv');
    }

    /**
     * 导出当前明细行（两种模式通用）
     */
    public function exportItems()
    {
        $isLinked = $this->formReturnType === PurchaseReturn::TYPE_LINKED;

        return response()->streamDownload(function () use ($isLinked) {
            echo "\xEF\xBB\xBF"."SKU编码,SKU名称,数量,单价(元),原因\n";
            foreach ($this->formItems as $row) {
                $sku = Sku::with('product')->find($row['sku_id'] ?? 0);
                $name = $isLinked ? ($row['sku_name'] ?? '') : ($sku?->product?->name ?? '');
                $code = $sku?->sku_code ?? '';
                $priceYuan = $isLinked
                    ? number_format(($row['price'] ?? 0) / 1000, 2, '.', '')
                    : ($row['price_yuan'] ?? '');
                echo implode(',', [$code, $name, $row['quantity'] ?? '', $priceYuan, $row['reason'] ?? ''])."\n";
            }
        }, '采购退货明细_'.now()->format('Ymd_His').'.csv');
    }

    public function save(): void
    {
        // 编辑模式：仅允许修改头字段
        if ($this->editingId) {
            $validated = $this->validate([
                'formSupplierId' => 'required|integer|min:1|exists:suppliers,id',
                'formWarehouseId' => 'required|integer|min:1|exists:warehouses,id',
                'formReason' => 'required|string|max:255',
                'formRemark' => 'nullable|string|max:500',
            ]);

            $item = PurchaseReturn::findOrFail($this->editingId);
            if (! $item->canEdit()) {
                $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');
                $this->showModal = false;
                $this->resetForm();

                return;
            }
            $item->update([
                'supplier_id' => $validated['formSupplierId'],
                'warehouse_id' => $validated['formWarehouseId'],
                'reason' => $validated['formReason'],
                'remark' => $validated['formRemark'],
            ]);
            $this->toastSuccess('退货单已更新');
            $this->showModal = false;
            $this->resetForm();

            return;
        }

        $this->validate([
            'formReason' => 'required|string|max:255',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $service = app(PurchaseReturnService::class);

        try {
            if ($this->formReturnType === PurchaseReturn::TYPE_LINKED) {
                $this->validate([
                    'formPurchaseOrderId' => 'required|integer|min:1|exists:purchase_orders,id',
                ]);

                $order = PurchaseOrder::findOrFail($this->formPurchaseOrderId);
                $payload = [];
                foreach ($this->formItems as $row) {
                    $qty = (int) ($row['quantity'] ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }
                    $payload[] = [
                        'purchase_order_item_id' => $row['purchase_order_item_id'],
                        'quantity' => $qty,
                        'reason' => $row['reason'] ?: null,
                    ];
                }

                $return = $service->createLinked($order, $payload, $this->formReason);
            } else {
                $this->validate([
                    'formSupplierId' => 'required|integer|min:1|exists:suppliers,id',
                    'formWarehouseId' => 'required|integer|min:1|exists:warehouses,id',
                ]);

                $payload = [];
                foreach ($this->formItems as $row) {
                    if (! ($row['sku_id'] ?? 0)) {
                        continue;
                    }
                    $payload[] = [
                        'sku_id' => (int) $row['sku_id'],
                        'quantity' => (int) ($row['quantity'] ?? 0),
                        'price' => money_to_cents($row['price_yuan'] ?? ''),
                        'reason' => ($row['reason'] ?? '') ?: null,
                    ];
                }

                $return = $service->createNormal($this->formSupplierId, $this->formWarehouseId, $payload, $this->formReason);
            }

            if ($this->formRemark !== '') {
                $return->update(['remark' => $this->formRemark]);
            }
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->toastSuccess("退货单 {$return->return_no} 已创建（{$return->return_type_label}，".money_format($return->total_amount).'）');
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        $item = PurchaseReturn::findOrFail($this->deletingId);
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

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formReturnType = PurchaseReturn::TYPE_LINKED;
        $this->formPurchaseOrderId = 0;
        $this->formSupplierId = 0;
        $this->formWarehouseId = 0;
        $this->formReason = '';
        $this->formRemark = '';
        $this->formItems = [];
        $this->itemsImportFile = null;
    }

    // ── 状态流转 ──

    public function approveReturn(int $id): void
    {
        try {
            $return = PurchaseReturn::findOrFail($id);
            $service = app(PurchaseReturnService::class);
            $service->approve($return);
            $this->toastSuccess('退货单已审核');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function shipReturn(int $id): void
    {
        try {
            $return = PurchaseReturn::findOrFail($id);
            $service = app(PurchaseReturnService::class);
            $service->ship($return);
            $this->toastSuccess('退货单已出库');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function completeReturn(int $id): void
    {
        try {
            $return = PurchaseReturn::findOrFail($id);
            $service = app(PurchaseReturnService::class);
            $service->complete($return);
            $this->toastSuccess('退货单已完成');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function cancelReturn(int $id): void
    {
        try {
            $return = PurchaseReturn::findOrFail($id);
            $service = app(PurchaseReturnService::class);
            $service->cancel($return);
            $this->toastSuccess('退货单已作废');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function getDefaultColumns(): array
    {
        return ['return_no', 'return_type', 'supplier_id', 'warehouse_id', 'status', 'total_amount', 'actual_amount'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'return_no' => $row->return_no,
                'return_type' => $row->return_type_label,
                'purchase_order_id' => $row->purchaseOrder?->order_no ?? '',
                'supplier_id' => $row->supplier?->name ?? '',
                'warehouse_id' => $row->warehouse?->name ?? '',
                'status' => $row->status,
                'total_amount' => money_format($row->total_amount, false),
                'reason' => $row->reason ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['return_no'];
    }

    public function getImportRequiredFields(): array
    {
        return ['供应商ID', '仓库ID', '退货原因'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'return_no', 'label' => '退货单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'return_type', 'label' => '类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'purchase_order_id', 'label' => '采购单', 'sortable' => true, 'exportable' => true],
            ['key' => 'supplier_id', 'label' => '供应商', 'sortable' => true, 'exportable' => true],
            ['key' => 'warehouse_id', 'label' => '仓库', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '总金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'actual_amount', 'label' => '实际金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'reason', 'label' => '退货原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return PurchaseReturn::with(['supplier', 'warehouse', 'purchaseOrder'])->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '采购退货_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PurchaseReturn::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '供应商ID' => 'supplier_id',
            '仓库ID' => 'warehouse_id',
            '退货原因' => 'reason',
        ];
    }

    public function getPageIds(): array
    {
        return PurchaseReturn::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = PurchaseReturn::with(['supplier', 'warehouse', 'purchaseOrder'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('return_no', 'like', "%{$this->search}%");
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(setting('per_page', 10));
        // 关联退货只能选已入库/已完成的采购单
        $purchaseOrders = PurchaseOrder::whereIn('status', [PurchaseOrder::STATUS_STOCKED, PurchaseOrder::STATUS_COMPLETED])
            ->orderBy('id', 'desc')->limit(50)->get();
        $suppliers = Supplier::enabled()->orderBy('name')->get();
        $warehouses = Warehouse::enabled()->get();
        $skus = Sku::with('product')->where('status', 1)->orderBy('id', 'desc')->limit(200)->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.purchase.purchase-return-list', compact('items', 'purchaseOrders', 'suppliers', 'warehouses', 'skus', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('采购退货');
    }
}
