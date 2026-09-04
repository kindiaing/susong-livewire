<?php

namespace App\Livewire\Purchase;

use App\Imports\PurchaseOrderImport;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderList extends Component
{
    use WithColumnVisibility, WithExcelExport, WithExcelImport, WithRowSelection;
    use WithListCrud;
    use WithPagination;
    use WithToast;

    protected string $modelClass = PurchaseOrder::class;

    public string $search = '';

    public int $filterStatus = -1;

    public int $formSupplierId = 0;

    public int $formWarehouseId = 0;

    public string $formPurchaseDate = '';

    public string $formRemark = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $order = PurchaseOrder::findOrFail($id);
        if (! $order->canEdit()) {
            $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');

            return;
        }
        $this->editingId = $id;
        $this->editingStatusLabel = $order->status_label;
        $this->editingStatusColor = 'yellow';
        $this->formSupplierId = $order->supplier_id;
        $this->formWarehouseId = $order->warehouse_id ?? 0;
        $this->formPurchaseDate = $order->purchase_date?->format('Y-m-d') ?? '';
        $this->formRemark = $order->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSupplierId' => 'required|integer|min:1',
            'formWarehouseId' => 'required|integer|min:1',
            'formPurchaseDate' => 'nullable|date',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'supplier_id' => $validated['formSupplierId'],
            'warehouse_id' => $validated['formWarehouseId'],
            'purchase_date' => $validated['formPurchaseDate'] ?: null,
            'remark' => $validated['formRemark'] ?: null,
        ];

        if ($this->editingId) {
            $order = PurchaseOrder::findOrFail($this->editingId);
            if (! $order->canEdit()) {
                $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');
                $this->showModal = false;
                $this->resetForm();

                return;
            }
            $order->update($data);
            $this->toastSuccess('采购单已更新');
        } else {
            $data['order_no'] = PurchaseOrder::generateOrderNo();
            $data['status'] = PurchaseOrder::STATUS_PENDING;
            $data['operator_id'] = Auth::id();
            $data['ordered_at'] = now();
            PurchaseOrder::create($data);
            $this->toastSuccess('采购单已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        $order = PurchaseOrder::findOrFail($this->deletingId);

        if (! $order->canEdit()) {
            $this->toastError('当前状态不可删除，仅待接单状态的采购单可删除');
            $this->showDeleteConfirm = false;
            $this->deletingId = null;

            return;
        }

        $order->delete();
        $this->toastSuccess('采购单已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function batchDelete(): void
    {
        abort_unless(auth()->user()?->can('purchase.purchase-order.delete'), 403, '无批量删除权限');

        $count = count($this->selectedIds);
        if ($count === 0) {
            $this->toastWarning('请先选择要删除的采购单');

            return;
        }
        $deletable = PurchaseOrder::whereIn('id', $this->selectedIds)
            ->where('status', PurchaseOrder::STATUS_PENDING)
            ->pluck('id')
            ->toArray();

        if (empty($deletable)) {
            $this->toastError('所选采购单均非待接单状态，不可删除');

            return;
        }

        PurchaseOrder::whereIn('id', $deletable)->delete();
        $this->toastSuccess('已删除 '.count($deletable).' 条采购单');
        $this->clearSelection();
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
        $this->formSupplierId = 0;
        $this->formWarehouseId = 0;
        $this->formPurchaseDate = '';
        $this->formRemark = '';
    }

    public function getDefaultColumns(): array
    {
        return ['order_no', 'supplier_id', 'purchase_date', 'status', 'total_amount'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'order_no' => $row->order_no,
                'supplier_id' => $row->supplier?->name ?? '',
                'status' => $row->status,
                'total_amount' => money_format($row->total_amount, false),
                'remark' => $row->remark ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return [];
    }

    public function getImportRequiredFields(): array
    {
        return ['供应商ID', '仓库ID', 'SKU ID', '采购数量', '采购单价(元)'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order_no', 'label' => '采购单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'supplier_id', 'label' => '供应商', 'sortable' => true, 'exportable' => true],
            ['key' => 'purchase_date', 'label' => '采购日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '总金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'actual_amount', 'label' => '实际金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    /**
     * 构建带筛选条件的查询（列表渲染与导出共用）
     */
    private function buildQuery()
    {
        $query = PurchaseOrder::with('supplier')
            ->withSum('items as discrepancy_total', 'discrepancy_quantity')
            ->withCount(['items as discrepancy_item_count' => fn ($q) => $q->where('discrepancy_quantity', '>', 0)])
            ->withCount(['items as discrepancy_loss_count' => fn ($q) => $q->where('discrepancy_quantity', '>', 0)->whereNotNull('loss_order_id')])
            ->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_no', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', function ($sq) {
                        $sq->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        return $query;
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '采购单_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PurchaseOrder::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '采购单号' => 'order_no',
            '供应商ID' => 'supplier_id',
            '仓库ID' => 'warehouse_id',
            '采购日期' => 'purchase_date',
            'SKU ID' => 'sku_id',
            '采购数量' => 'quantity',
            '采购单价(元)' => 'price',
            '明细备注' => 'item_remark',
            '备注' => 'remark',
        ];
    }

    /**
     * 模板描述行：每列对应一行描述文字，方便熟练操作人手写模板
     * key 为中文表头，value 为描述文字
     */
    public function getImportTemplateDescriptionRow(): array
    {
        return [
            '采购单号' => '留空自动生成，填则需唯一',
            '供应商ID' => '系统供应商ID，必填',
            '仓库ID' => '系统仓库ID，必填',
            '采购日期' => '格式 2026-01-01，可空',
            'SKU ID' => '系统SKU ID，必填',
            '采购数量' => '最小单位数量，整数，必填',
            '采购单价(元)' => '单位元，如 12.50，必填',
            '明细备注' => '该行明细备注，可空',
            '备注' => '采购单头备注，同单号取首行',
        ];
    }

    /**
     * 覆写导入逻辑：采购单需创建头+明细，使用专用 Import 类
     */
    public function doImport(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $this->importErrors = [];

        try {
            $import = new PurchaseOrderImport();

            Excel::import($import, $this->importFile);

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $errors = $import->getErrorRows();

            $this->importErrors = $errors;

            $msg = "成功导入 {$imported} 张采购单";
            if ($skipped > 0) {
                $msg .= "，跳过 {$skipped} 张";
            }
            if (! empty($errors)) {
                $msg .= '，失败 '.count($errors).' 行';
            }

            $this->importMessage = $msg;
            if (! empty($errors)) {
                $this->toastError($msg);
            } elseif ($skipped > 0) {
                $this->toastWarning($msg);
            } else {
                $this->toastSuccess($msg);
            }
        } catch (\Exception $e) {
            $this->importMessage = '导入失败：'.$e->getMessage();
            $this->toastError($this->importMessage);
        }

        $this->importFile = null;
    }

    public function getPageIds(): array
    {
        return PurchaseOrder::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    /**
     * 供应商服务端搜索（searchable-select 调用，分页协议）
     */
    public function searchSupplierOptions(string $keyword = '', int $page = 1): array
    {
        $keyword = trim($keyword);
        $query = Supplier::query();
        if ($keyword !== '') {
            $query->where('name', 'like', "%{$keyword}%");
        }

        return build_search_options($query->orderBy('name'), $page);
    }

    public function render()
    {
        $orders = $this->buildQuery()->paginate(setting('per_page', 10));
        $warehouses = Warehouse::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.purchase.purchase-order-list', compact('orders', 'warehouses', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('采购单管理');
    }
}
