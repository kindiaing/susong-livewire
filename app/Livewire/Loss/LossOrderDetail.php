<?php

namespace App\Livewire\Loss;

use App\Exports\GenericExport;
use App\Imports\GenericImport;
use App\Livewire\Traits\WithToast;
use App\Models\LossOrder;
use App\Models\LossOrderItem;
use App\Models\Sku;
use App\Models\Supplier;
use App\Services\LossOrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class LossOrderDetail extends Component
{
    use WithToast;
    use WithFileUploads;

    public LossOrder $lossOrder;

    // 审核弹窗
    public bool $showApproveConfirm = false;

    // 关闭确认弹窗（关闭后单据不可再操作）
    public bool $showCloseConfirm = false;

    // 明细新增/编辑
    public bool $showItemModal = false;
    public ?int $editingItemId = null;
    public int $itemSkuId = 0;
    public string $itemCostPriceYuan = '';
    public string $itemQuantity = '';
    public int $itemResponsibleParty = LossOrderItem::PARTY_PLATFORM;
    public int $itemSupplierId = 0;
    public string $itemReason = '';

    // 明细导入
    public bool $showImportModal = false;
    public $importFile = null;
    public string $importMessage = '';
    public array $importErrors = [];

    // 明细导出
    public bool $showExportModal = false;
    public array $exportColumns = [];
    public string $exportFormat = 'xlsx';

    public function mount(int $id): void
    {
        $this->loadLossOrder($id);
    }

    public function loadLossOrder(int $id): void
    {
        $this->lossOrder = LossOrder::with(['warehouse', 'items.sku', 'items.supplier', 'applicant', 'reviewer', 'auditLogs.operator'])
            ->findOrFail($id);
    }

    // ══════════════════════════════════════
    //  审核
    // ══════════════════════════════════════

    public function confirmApprove(): void
    {
        $this->showApproveConfirm = true;
    }

    public function closeApproveConfirm(): void
    {
        $this->showApproveConfirm = false;
        $this->resetErrorBag();
    }

    public function approve(): void
    {
        if ($this->lossOrder->approval_status !== LossOrder::APPROVAL_PENDING) {
            $this->toastError('该单不在待审核状态');
            $this->showApproveConfirm = false;

            return;
        }

        $this->lossOrder->update([
            'approval_status' => LossOrder::APPROVAL_APPROVED,
            'status' => LossOrder::STATUS_APPROVED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->loadLossOrder($this->lossOrder->id);
        $this->showApproveConfirm = false;
        $this->toastSuccess('损耗单已审核通过');
    }

    public function reject(): void
    {
        if ($this->lossOrder->approval_status !== LossOrder::APPROVAL_PENDING) {
            $this->toastError('该单不在待审核状态');
            $this->showApproveConfirm = false;

            return;
        }

        $this->lossOrder->update([
            'approval_status' => LossOrder::APPROVAL_REJECTED,
            'status' => LossOrder::STATUS_CANCELLED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->loadLossOrder($this->lossOrder->id);
        $this->showApproveConfirm = false;
        $this->toastSuccess('损耗单已拒绝');
    }

    // ══════════════════════════════════════
    //  执行 / 关闭
    // ══════════════════════════════════════

    public function execute(): void
    {
        try {
            app(LossOrderService::class)->execute(
                LossOrder::findOrFail($this->lossOrder->id)
            );
            $this->loadLossOrder($this->lossOrder->id);
            $this->toastSuccess('损耗单已执行，库存已扣减');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function close(): void
    {
        $purchaseTraceClosable = $this->lossOrder->status === LossOrder::STATUS_APPROVED
            && $this->lossOrder->source_type === 'purchase_order';

        if ($this->lossOrder->status !== LossOrder::STATUS_EXECUTED && !$purchaseTraceClosable) {
            $this->toastError('只有已执行的单据可关闭');

            return;
        }

        $this->lossOrder->update([
            'status' => LossOrder::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        $this->loadLossOrder($this->lossOrder->id);
        $this->showCloseConfirm = false;
        $this->toastSuccess('损耗单已关闭');
    }

    public function confirmClose(): void
    {
        $this->showCloseConfirm = true;
    }

    public function closeCloseConfirm(): void
    {
        $this->showCloseConfirm = false;
    }

    // ══════════════════════════════════════
    //  明细管理：新增 / 编辑 / 删除
    // ══════════════════════════════════════

    public function openAddItemModal(): void
    {
        $this->resetItemForm();
        $this->showItemModal = true;
    }

    public function openEditItemModal(int $id): void
    {
        $item = LossOrderItem::findOrFail($id);
        $this->editingItemId = $id;
        $this->itemSkuId = $item->sku_id;
        $this->itemCostPriceYuan = number_format($item->cost_price / 1000, 2);
        $this->itemQuantity = (string) $item->quantity;
        $this->itemResponsibleParty = $item->responsible_party;
        $this->itemSupplierId = $item->supplier_id ?? 0;
        $this->itemReason = $item->reason ?? '';
        $this->showItemModal = true;
    }

    public function saveItem(): void
    {
        $validated = $this->validate([
            'itemSkuId' => 'required|integer|min:1',
            'itemCostPriceYuan' => 'required|numeric|min:0',
            'itemQuantity' => 'required|integer|min:1',
            'itemResponsibleParty' => 'required|in:1,2',
            'itemSupplierId' => 'nullable|integer|min:0',
            'itemReason' => 'nullable|string|max:255',
        ], [
            'itemSkuId.min' => '请选择 SKU',
            'itemQuantity.min' => '数量必须大于 0',
            'itemCostPriceYuan.required' => '成本价不能为空',
        ]);

        $costPriceCents = (int) round((float) $validated['itemCostPriceYuan'] * 1000);
        $quantity = (int) $validated['itemQuantity'];
        $lossAmount = $costPriceCents * $quantity;

        $itemData = [
            'sku_id' => $validated['itemSkuId'],
            'loss_type' => $this->lossOrder->loss_type,
            'quantity' => $quantity,
            'cost_price' => $costPriceCents,
            'loss_amount' => $lossAmount,
            'responsible_party' => $validated['itemResponsibleParty'],
            'supplier_id' => $validated['itemResponsibleParty'] === LossOrderItem::PARTY_SUPPLIER ? ($validated['itemSupplierId'] ?: null) : null,
            'reason' => $validated['itemReason'] ?: null,
        ];

        DB::transaction(function () use ($itemData) {
            if ($this->editingItemId) {
                $item = LossOrderItem::findOrFail($this->editingItemId);
                $oldAmount = $item->loss_amount;
                $item->update($itemData);
                $this->lossOrder->update([
                    'total_amount' => $this->lossOrder->total_amount - $oldAmount + $itemData['loss_amount'],
                ]);
            } else {
                $itemData['loss_order_id'] = $this->lossOrder->id;
                LossOrderItem::create($itemData);
                $this->lossOrder->update([
                    'total_amount' => $this->lossOrder->total_amount + $itemData['loss_amount'],
                ]);
            }
        });

        $this->loadLossOrder($this->lossOrder->id);
        $this->showItemModal = false;
        $this->resetItemForm();
        $this->toastSuccess($this->editingItemId ? '明细已更新' : '明细已添加');
    }

    public function deleteItem(int $id): void
    {
        $item = LossOrderItem::findOrFail($id);
        $oldAmount = $item->loss_amount;
        $item->delete();
        $this->lossOrder->update([
            'total_amount' => $this->lossOrder->total_amount - $oldAmount,
        ]);
        $this->loadLossOrder($this->lossOrder->id);
        $this->toastSuccess('明细已删除');
    }

    public function closeItemModal(): void
    {
        $this->showItemModal = false;
        $this->resetItemForm();
        $this->resetErrorBag();
    }

    private function resetItemForm(): void
    {
        $this->editingItemId = null;
        $this->itemSkuId = 0;
        $this->itemCostPriceYuan = '';
        $this->itemQuantity = '';
        $this->itemResponsibleParty = LossOrderItem::PARTY_PLATFORM;
        $this->itemSupplierId = 0;
        $this->itemReason = '';
    }

    /**
     * SKU 选择时自动带出成本价
     */
    public function updatedItemSkuId($value): void
    {
        $skuId = (int) $value;
        if ($skuId <= 0) {
            return;
        }
        $sku = Sku::find($skuId);
        if ($sku) {
            $costPrice = $sku->cost_price > 0 ? $sku->cost_price : $sku->purchase_price;
            $this->itemCostPriceYuan = number_format($costPrice / 1000, 2);
        }
    }

    // ══════════════════════════════════════
    //  明细导出
    // ══════════════════════════════════════

    public function openExportModal(): void
    {
        $this->exportColumns = array_keys($this->getItemExportColumns());
        $this->showExportModal = true;
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    public function toggleExportColumn(string $column): void
    {
        $idx = array_search($column, $this->exportColumns);
        if ($idx !== false) {
            unset($this->exportColumns[$idx]);
            $this->exportColumns = array_values($this->exportColumns);
        } else {
            $this->exportColumns[] = $column;
        }
    }

    public function doExport()
    {
        if (empty($this->exportColumns)) {
            $this->toastError('请至少选择一列导出');

            return;
        }

        $allColumns = $this->getItemExportColumns();
        $columnLabels = collect($allColumns)
            ->filter(fn($label, $key) => in_array($key, $this->exportColumns))
            ->toArray();

        $export = new GenericExport(
            queryOrData: $this->lossOrder->items()->with(['sku.product', 'supplier'])->get(),
            columns: $this->exportColumns,
            columnLabels: $columnLabels,
            rowCallback: function ($item) {
                return [
                    'sku_code' => $item->sku?->sku_code ?? '',
                    'sku_name' => $item->sku?->product?->name ?? '',
                    'quantity' => $item->quantity,
                    'cost_price' => money_format($item->cost_price, false),
                    'loss_amount' => money_format($item->loss_amount, false),
                    'responsible_party' => $item->responsible_party == 2 ? '供应商' : '平台',
                    'supplier' => $item->supplier?->name ?? '',
                    'reason' => $item->reason ?? '',
                ];
            },
        );

        $this->showExportModal = false;

        return Excel::download(
            $export,
            '损耗明细_' . $this->lossOrder->loss_no . '.' . $this->exportFormat,
            $this->exportFormat === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX,
        );
    }

    public function getItemExportColumns(): array
    {
        return [
            'sku_code' => 'SKU编码',
            'sku_name' => '商品名称',
            'quantity' => '数量',
            'cost_price' => '成本价',
            'loss_amount' => '损耗金额',
            'responsible_party' => '责任方',
            'supplier' => '供应商',
            'reason' => '原因',
        ];
    }

    // ══════════════════════════════════════
    //  明细导入
    // ══════════════════════════════════════

    public function openImportModal(): void
    {
        $this->importFile = null;
        $this->importMessage = '';
        $this->importErrors = [];
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importMessage = '';
        $this->importErrors = [];
    }

    public function downloadImportTemplate()
    {
        $columnMap = $this->getImportColumnMap();
        $requiredHeadings = $this->getImportRequiredFields();

        $export = new GenericExport(
            queryOrData: [],
            columns: array_keys($columnMap),
            columnLabels: [],
            requiredHeadings: $requiredHeadings,
        );

        return Excel::download(
            $export,
            '损耗明细导入模板_' . $this->lossOrder->loss_no . '.xlsx',
        );
    }

    public function doImport(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $this->importErrors = [];

        try {
            $rows = Excel::toCollection(new class implements \Maatwebsite\Excel\Concerns\ToCollection {
                public function collection(\Illuminate\Support\Collection $rows)
                {
                    return $rows;
                }
            }, $this->importFile);

            $imported = 0;
            $errors = [];
            $columnMap = $this->getImportColumnMap();
            $skus = Sku::with('product')->get()->keyBy('sku_code');

            // 第一行是表头
            $headerRow = $rows->first();
            if (!$headerRow || $headerRow->count() < 2) {
                $this->importMessage = '导入失败：文件为空或格式不正确';
                $this->toastError($this->importMessage);

                return;
            }

            $headers = $headerRow->first()->toArray();
            $dataRows = $rows->slice(1);

            foreach ($dataRows as $index => $row) {
                $rowData = [];
                foreach ($headers as $colIndex => $headerName) {
                    $field = $columnMap[$headerName] ?? null;
                    if ($field && isset($row[$colIndex])) {
                        $rowData[$field] = $row[$colIndex];
                    }
                }

                if (empty($rowData['sku_code'])) {
                    $errors[] = ['row' => $index + 2, 'error' => 'SKU编码不能为空'];
                    continue;
                }

                $sku = $skus->get($rowData['sku_code']);
                if (!$sku) {
                    $errors[] = ['row' => $index + 2, 'error' => "SKU编码 {$rowData['sku_code']} 不存在"];
                    continue;
                }

                $quantity = (int) ($rowData['quantity'] ?? 0);
                if ($quantity <= 0) {
                    $errors[] = ['row' => $index + 2, 'error' => '数量必须大于0'];
                    continue;
                }

                $costPriceYuan = (float) ($rowData['cost_price'] ?? 0);
                $costPriceCents = (int) round($costPriceYuan * 1000);
                $lossAmount = $costPriceCents * $quantity;

                $responsibleParty = LossOrderItem::PARTY_PLATFORM;
                if (isset($rowData['responsible_party']) && $rowData['responsible_party'] === '供应商') {
                    $responsibleParty = LossOrderItem::PARTY_SUPPLIER;
                }

                $supplierId = null;
                if ($responsibleParty === LossOrderItem::PARTY_SUPPLIER && !empty($rowData['supplier'])) {
                    $supplier = Supplier::where('name', $rowData['supplier'])->first();
                    if ($supplier) {
                        $supplierId = $supplier->id;
                    }
                }

                LossOrderItem::create([
                    'loss_order_id' => $this->lossOrder->id,
                    'sku_id' => $sku->id,
                    'loss_type' => $this->lossOrder->loss_type,
                    'quantity' => $quantity,
                    'cost_price' => $costPriceCents,
                    'loss_amount' => $lossAmount,
                    'responsible_party' => $responsibleParty,
                    'supplier_id' => $supplierId,
                    'reason' => $rowData['reason'] ?? null,
                ]);

                $this->lossOrder->update([
                    'total_amount' => $this->lossOrder->total_amount + $lossAmount,
                ]);

                $imported++;
            }

            $this->importErrors = $errors;

            $msg = "成功导入 {$imported} 条";
            if (!empty($errors)) {
                $msg .= '，失败 ' . count($errors) . ' 条';
            }

            $this->importMessage = $msg;
            if (!empty($errors)) {
                $this->toastError($msg);
            } else {
                $this->toastSuccess($msg);
            }

            $this->loadLossOrder($this->lossOrder->id);
        } catch (\Exception $e) {
            $this->importMessage = '导入失败：' . $e->getMessage();
            $this->toastError($this->importMessage);
        }

        $this->importFile = null;
    }

    public function getImportColumnMap(): array
    {
        return [
            'SKU编码' => 'sku_code',
            '数量' => 'quantity',
            '成本价' => 'cost_price',
            '责任方' => 'responsible_party',
            '供应商' => 'supplier',
            '原因' => 'reason',
        ];
    }

    public function getImportRequiredFields(): array
    {
        return ['SKU编码', '数量', '成本价'];
    }

    // ══════════════════════════════════════
    //  Render
    // ══════════════════════════════════════

    public function render()
    {
        $statusMap = LossOrder::statusMap();
        $statusColorMap = [
            LossOrder::STATUS_PENDING => 'yellow',
            LossOrder::STATUS_APPROVED => 'blue',
            LossOrder::STATUS_EXECUTED => 'green',
            LossOrder::STATUS_CLOSED => 'gray',
            LossOrder::STATUS_CANCELLED => 'red',
        ];
        $approvalStatusMap = LossOrder::approvalStatusMap();
        $approvalColorMap = [
            LossOrder::APPROVAL_PENDING => 'yellow',
            LossOrder::APPROVAL_APPROVED => 'green',
            LossOrder::APPROVAL_REJECTED => 'red',
        ];
        $typeMap = LossOrder::typeMap();
        $sourceTypeMap = LossOrder::sourceTypeMap();
        $responsiblePartyMap = LossOrderItem::responsiblePartyMap();

        $skus = Sku::with('product')->where('status', 1)->orderBy('id', 'desc')->limit(200)->get();
        $itemExportColumns = $this->getItemExportColumns();
        $canEditItems = $this->lossOrder->canEdit();

        return view('livewire.loss.loss-order-detail', compact(
            'statusMap', 'statusColorMap', 'approvalStatusMap', 'approvalColorMap',
            'typeMap', 'sourceTypeMap', 'responsiblePartyMap',
            'skus', 'itemExportColumns', 'canEditItems'
        ))
            ->layout('components.app-layout')
            ->title("损耗单详情 {$this->lossOrder->loss_no}");
    }

    /**
     * 供应商服务端搜索（searchable-select 调用）
     */
    public function searchSupplierOptions(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        $query = Supplier::query();
        if ($keyword !== '') {
            $query->where('name', 'like', "%{$keyword}%");
        }

        return $query->orderBy('name')->limit(20)->get(['id', 'name'])
            ->map(fn ($s) => ['value' => $s->id, 'label' => $s->name])
            ->toArray();
    }
}
