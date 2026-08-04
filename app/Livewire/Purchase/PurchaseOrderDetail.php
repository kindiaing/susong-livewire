<?php

namespace App\Livewire\Purchase;

use App\Exports\GenericExport;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Sku;
use App\Services\PurchaseService;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderDetail extends Component
{
    use WithToast;
    use WithFileUploads;
    use WithMoneyConversion;

    public PurchaseOrder $order;
    public $items;

    // 明细编辑
    public bool $showAddItemModal = false;
    public int $formSkuId = 0;
    public int $formQuantity = 0;
    public string $formPrice = '';

    // 入库弹窗
    public bool $showStockInModal = false;
    public int $stockInWarehouseId = 0;
    public string $stockInBatchNo = '';
    public array $stockInItems = [];

    // 状态操作确认
    public bool $showConfirmModal = false;
    public string $confirmAction = '';
    public string $confirmTitle = '';

    // 导出
    public bool $showExportModal = false;
    public bool $exportActual = false;
    public bool $exportDiscrepancy = false;

    // 导入
    public bool $showImportModal = false;
    public $importFile = null;

    public function mount(int $id): void
    {
        $this->loadOrder($id);
    }

    public function loadOrder(int $id): void
    {
        $this->order = PurchaseOrder::with(['supplier', 'warehouse', 'operator', 'items.sku.product'])->findOrFail($id);
        $this->items = $this->order->items;
    }

    // ── 状态流转 ──

    public function confirmSubmit(): void
    {
        $this->confirmAction = 'submit';
        $this->confirmTitle = '确认提交采购单？';
        $this->showConfirmModal = true;
    }

    public function confirmShip(): void
    {
        $this->confirmAction = 'ship';
        $this->confirmTitle = '确认标记为已发货？';
        $this->showConfirmModal = true;
    }

    public function confirmComplete(): void
    {
        $this->confirmAction = 'complete';
        $this->confirmTitle = '确认完成此采购单？';
        $this->showConfirmModal = true;
    }

    public function confirmCancel(): void
    {
        $this->confirmAction = 'cancel';
        $this->confirmTitle = '确认作废此采购单？此操作不可撤销。';
        $this->showConfirmModal = true;
    }

    public function executeConfirm(): void
    {
        try {
            $service = app(PurchaseService::class);

            match ($this->confirmAction) {
                'submit' => $service->submit($this->order),
                'ship' => $service->ship($this->order),
                'complete' => $service->complete($this->order),
                'cancel' => $service->cancel($this->order),
                default => null,
            };

            $this->loadOrder($this->order->id);
            $this->showConfirmModal = false;
            $this->toastSuccess('操作成功');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->resetErrorBag();
    }

    // ── 添加明细 ──

    public function openAddItemModal(): void
    {
        $this->resetAddItemForm();
        $this->showAddItemModal = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'formSkuId' => 'required|integer|min:1',
            'formQuantity' => 'required|integer|min:1',
            'formPrice' => 'required|numeric|min:0',
        ]);

        try {
            $service = app(PurchaseService::class);
            $service->addItem($this->order, $this->formSkuId, $this->formQuantity, money_to_cents($this->formPrice));
            $this->loadOrder($this->order->id);
            $this->showAddItemModal = false;
            $this->toastSuccess('明细已添加');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function removeItem(int $itemId): void
    {
        try {
            $item = PurchaseOrderItem::findOrFail($itemId);
            $service = app(PurchaseService::class);
            $service->removeItem($item);
            $this->loadOrder($this->order->id);
            $this->toastSuccess('明细已删除');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeAddItemModal(): void
    {
        $this->showAddItemModal = false;
        $this->resetErrorBag();
    }

    // ── 入库操作 ──

    public function openStockInModal(): void
    {
        $this->stockInWarehouseId = 0;
        $this->stockInBatchNo = 'IN' . date('YmdHis');
        $this->stockInItems = [];

        foreach ($this->order->items as $item) {
            $this->stockInItems[] = [
                'id' => $item->id,
                'sku_name' => $item->sku?->sku_code . ' - ' . ($item->sku?->product?->name ?? ''),
                'quantity' => $item->quantity,
                'price' => $this->centsToYuan($item->price),
                'actual_quantity' => $item->quantity,
                'actual_price' => $this->centsToYuan($item->price),
                'discrepancy_reason' => '',
            ];
        }

        $this->showStockInModal = true;
    }

    public function executeStockIn(): void
    {
        $this->validate([
            'stockInWarehouseId' => 'required|integer|min:1',
            'stockInBatchNo' => 'required|string|max:50',
            'stockInItems' => 'required|array|min:1',
        ]);

        try {
            $service = app(PurchaseService::class);
            $items = collect($this->stockInItems)->map(function ($item) {
                $item['price'] = money_to_cents($item['price']);
                $item['actual_price'] = money_to_cents($item['actual_price']);
                return $item;
            })->toArray();
            $service->stockIn($this->order, $this->stockInWarehouseId, $items, $this->stockInBatchNo);
            $this->loadOrder($this->order->id);
            $this->showStockInModal = false;
            $this->toastSuccess('入库成功');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeStockInModal(): void
    {
        $this->showStockInModal = false;
        $this->resetErrorBag();
    }

    // ── 导出 ──

    public function openExportModal(): void
    {
        $this->showExportModal = true;
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    public function executeExport(): void
    {
        $columns = ['SKU编码', '商品名称', '采购数量', '采购单价', '采购金额'];

        if ($this->exportActual) {
            $columns[] = '实际数量';
            $columns[] = '实际金额';
        }
        if ($this->exportDiscrepancy) {
            $columns[] = '差异原因';
        }

        $items = $this->order->items()->with('sku.product')->get();

        $data = $items->map(function ($item) {
            $row = [
                $item->sku?->sku_code ?? '',
                $item->sku?->product?->name ?? '',
                $item->quantity,
                $item->price,
                $item->amount,
            ];

            if ($this->exportActual) {
                $row[] = $item->actual_quantity ?: '';
                $row[] = $item->actual_amount ?: '';
            }
            if ($this->exportDiscrepancy) {
                $row[] = $item->discrepancy_reason ?? '';
            }

            return $row;
        })->toArray();

        $fileName = '采购明细_' . $this->order->order_no . '_' . now()->format('Ymd_His') . '.xlsx';

        $this->showExportModal = false;

        Excel::download(new GenericExport($data, $columns), $fileName);
    }

    // ── 导入 ──

    public function openImportModal(): void
    {
        $this->importFile = null;
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->resetErrorBag();
    }

    public function executeImport(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $rows = Excel::toCollection(null, $this->importFile)->first();
            $imported = 0;

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // 跳过标题行

                $skuCode = trim($row[0] ?? '');
                $quantity = (int) ($row[1] ?? 0);
                $price = money_to_cents($row[2] ?? 0);

                if (! $skuCode || $quantity <= 0) continue;

                $sku = Sku::where('sku_code', $skuCode)->first();
                if (! $sku) continue;

                try {
                    $service = app(PurchaseService::class);
                    $service->addItem($this->order, $sku->id, $quantity, $price);
                    $imported++;
                } catch (\Exception $e) {
                    continue;
                }
            }

            $this->loadOrder($this->order->id);
            $this->showImportModal = false;
            $this->toastSuccess("成功导入 {$imported} 条明细");
        } catch (\Exception $e) {
            $this->toastError('导入失败：' . $e->getMessage());
        }
    }

    private function resetAddItemForm(): void
    {
        $this->formSkuId = 0;
        $this->formQuantity = 0;
        $this->formPrice = '';
    }

    public function render()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::enabled()->orderBy('name')->get();
        $skus = Sku::with('product')->orderBy('sku_code')->get();

        return view('livewire.purchase.purchase-order-detail', compact('suppliers', 'warehouses', 'skus'))
            ->layout('components.app-layout')
            ->title("采购单 {$this->order->order_no}");
    }
}
