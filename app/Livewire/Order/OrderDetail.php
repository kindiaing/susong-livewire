<?php

namespace App\Livewire\Order;

use App\Exports\GenericExport;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sku;
use App\Services\PricingService;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class OrderDetail extends Component
{
    use WithToast;
    use WithMoneyConversion;
    use WithFileUploads;

    public Order $order;
    public $items;

    // 明细编辑
    public bool $showAddItemModal = false;
    public ?int $editingItemId = null;
    public int $formSkuId = 0;
    public int $formQuantity = 0;
    public string $formPrice = '';
    public string $formRemark = '';
    public bool $showDeleteItemConfirm = false;
    public ?int $deletingItemId = null;

    // 添加明细确认
    public bool $showSaveConfirm = false;
    public string $saveConfirmTitle = '';

    // 取消确认（表单有数据时）
    public bool $showCancelConfirm = false;

    // 状态操作确认
    public bool $showConfirmModal = false;
    public string $confirmAction = '';
    public string $confirmTitle = '';

    // 导出
    public bool $showExportModal = false;
    public bool $exportActual = false;
    public bool $exportStrategy = false;

    // 导入
    public bool $showImportModal = false;
    public $importFile = null;

    public function mount(int $id): void
    {
        $this->loadOrder($id);
    }

    public function getIsSuperAdminProperty(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public function getCanEditItemsProperty(): bool
    {
        return $this->order->status === Order::STATUS_PICKING_WAIT
            || ($this->isSuperAdmin && $this->order->status !== Order::STATUS_CANCELLED);
    }

    public function loadOrder(int $id): void
    {
        $this->order = Order::with(['merchant', 'deliveryRoute', 'items.sku.product'])->findOrFail($id);
        $this->items = $this->order->items;
    }

    // ── 状态流转 ──

    public function confirmSubmit(): void
    {
        $this->confirmAction = 'submit';
        $this->confirmTitle = '确认提交此订单？';
        $this->showConfirmModal = true;
    }

    public function confirmPick(): void
    {
        $this->confirmAction = 'pick';
        $this->confirmTitle = '确认开始拣货？';
        $this->showConfirmModal = true;
    }

    public function confirmDeliver(): void
    {
        $this->confirmAction = 'deliver';
        $this->confirmTitle = '确认开始配送？';
        $this->showConfirmModal = true;
    }

    public function confirmSign(): void
    {
        $this->confirmAction = 'sign';
        $this->confirmTitle = '确认签收？';
        $this->showConfirmModal = true;
    }

    public function confirmCancel(): void
    {
        $this->confirmAction = 'cancel';
        $this->confirmTitle = '确认取消此订单？此操作不可撤销。';
        $this->showConfirmModal = true;
    }

    // ── 超管状态回退 ──

    public function confirmRollback(string $toStatus): void
    {
        $statusLabels = Order::statusMap();
        $this->confirmAction = 'rollback_' . $toStatus;
        $this->confirmTitle = "确认回退到【{$statusLabels[$toStatus]}】状态？";
        $this->showConfirmModal = true;
    }

    public function executeConfirm(): void
    {
        try {
            if (str_starts_with($this->confirmAction, 'rollback_')) {
                $toStatus = (int) substr($this->confirmAction, 9);
                $this->order->update(['status' => $toStatus]);
                $this->loadOrder($this->order->id);
                $this->showConfirmModal = false;
                $this->toastSuccess('状态已回退');
                return;
            }

            match ($this->confirmAction) {
                'submit' => $this->order->update(['status' => Order::STATUS_PICKING]),
                'pick' => $this->order->update(['status' => Order::STATUS_PICKING]),
                'deliver' => $this->order->update(['status' => Order::STATUS_DELIVERING]),
                'sign' => $this->order->update(['status' => Order::STATUS_SIGNED]),
                'cancel' => $this->order->update(['status' => Order::STATUS_CANCELLED]),
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

    // ── 明细编辑 ──

    public function openAddItemModal(): void
    {
        $this->resetAddItemForm();
        $this->editingItemId = null;
        $this->showAddItemModal = true;
    }

    public function openEditItemModal(int $itemId): void
    {
        $item = OrderItem::findOrFail($itemId);
        $this->editingItemId = $itemId;
        $this->formSkuId = $item->sku_id;
        $this->formQuantity = $item->quantity;
        $this->formPrice = $this->centsToYuan($item->price);
        $this->formRemark = '';
        $this->showAddItemModal = true;
    }

    /**
     * 点击保存 → 先验证，再弹确认
     */
    public function saveItem(): void
    {
        $this->validate([
            'formSkuId' => 'required|integer|min:1',
            'formQuantity' => 'required|integer|min:1',
            'formPrice' => 'required|numeric|min:0',
        ]);

        $sku = Sku::with('product')->find($this->formSkuId);
        $price = money_to_cents($this->formPrice);
        $subtotal = $price * $this->formQuantity;
        $skuLabel = $sku ? ($sku->sku_code . ' - ' . ($sku->product?->name ?? '')) : '';

        if ($this->editingItemId) {
            $this->saveConfirmTitle = "确认保存修改？";
        } else {
            $this->saveConfirmTitle = "确认添加明细？";
        }
        $this->saveConfirmTitle .= "\n\n商品：{$skuLabel}　数量：{$this->formQuantity}　单价：{$this->formPrice}元　小计：" . money_format($subtotal);

        $this->showSaveConfirm = true;
    }

    /**
     * 确认保存 → 执行写入
     */
    public function executeSaveItem(): void
    {
        try {
            $sku = Sku::with('product')->findOrFail($this->formSkuId);
            $price = money_to_cents($this->formPrice);
            $subtotal = $price * $this->formQuantity;

            if ($this->editingItemId) {
                $item = OrderItem::findOrFail($this->editingItemId);
                $item->update([
                    'sku_id' => $this->formSkuId,
                    'product_name' => $sku->product?->name ?? '',
                    'sku_specs' => $sku->specs ?? null,
                    'quantity' => $this->formQuantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'actual_quantity' => $this->formQuantity,
                    'actual_price' => $price,
                    'actual_subtotal' => $subtotal,
                ]);
                $this->toastSuccess('明细已更新');
            } else {
                OrderItem::create([
                    'order_id' => $this->order->id,
                    'sku_id' => $this->formSkuId,
                    'product_name' => $sku->product?->name ?? '',
                    'sku_specs' => $sku->specs ?? null,
                    'quantity' => $this->formQuantity,
                    'price' => $price,
                    'actual_quantity' => $this->formQuantity,
                    'actual_price' => $price,
                    'subtotal' => $subtotal,
                    'actual_subtotal' => $subtotal,
                    'strategy_price' => 0,
                    'strategy_amount' => 0,
                    'discrepancy_amount' => 0,
                    'status' => OrderItem::STATUS_NORMAL,
                ]);
                $this->toastSuccess('明细已添加');
            }

            $this->recalculateTotal();
            $this->loadOrder($this->order->id);
            $this->showSaveConfirm = false;
            $this->showAddItemModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeSaveConfirm(): void
    {
        $this->showSaveConfirm = false;
    }

    /**
     * 取消添加/编辑：如果表单有填写数据，弹确认；否则直接关闭
     */
    public function cancelAddItem(): void
    {
        if ($this->formSkuId > 0 || $this->formQuantity > 0 || $this->formPrice !== '') {
            $this->showCancelConfirm = true;
        } else {
            $this->closeAddItemModal();
        }
    }

    /**
     * 确认放弃编辑
     */
    public function confirmCancelEdit(): void
    {
        $this->showCancelConfirm = false;
        $this->closeAddItemModal();
    }

    public function closeCancelConfirm(): void
    {
        $this->showCancelConfirm = false;
    }

    public function confirmDeleteItem(int $itemId): void
    {
        $this->deletingItemId = $itemId;
        $this->showDeleteItemConfirm = true;
    }

    public function deleteItem(): void
    {
        try {
            $item = OrderItem::findOrFail($this->deletingItemId);
            $item->delete();
            $this->recalculateTotal();
            $this->loadOrder($this->order->id);
            $this->showDeleteItemConfirm = false;
            $this->deletingItemId = null;
            $this->toastSuccess('明细已删除');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeDeleteItemConfirm(): void
    {
        $this->showDeleteItemConfirm = false;
        $this->deletingItemId = null;
    }

    public function closeAddItemModal(): void
    {
        $this->showAddItemModal = false;
        $this->resetErrorBag();
    }

    /**
     * 通过 PricingService 自动取价
     */
    public function autoPrice(): void
    {
        $this->validateOnly('formSkuId', [
            'formSkuId' => 'required|integer|min:1',
        ]);

        try {
            $sku = Sku::findOrFail($this->formSkuId);
            $pricingService = app(PricingService::class);
            $result = $pricingService->calculate(
                $sku,
                'miniapp',
                $this->order->merchant_id,
            );
            $this->formPrice = $this->centsToYuan($result['price']);
        } catch (\Exception $e) {
            $this->toastError('取价失败：' . $e->getMessage());
        }
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
        $columns = ['SKU编码', '商品名称', '规格', '数量', '单价', '小计'];
        $moneyColumns = ['单价', '小计'];

        if ($this->exportStrategy) {
            $columns[] = '促销价';
            $moneyColumns[] = '促销价';
        }

        if ($this->exportActual) {
            $columns[] = '实际数量';
            $columns[] = '实际小计';
            $moneyColumns[] = '实际小计';
        }

        $items = $this->order->items()->with('sku.product')->get();

        $data = $items->map(function ($item) {
            $row = [
                $item->sku?->sku_code ?? '',
                $item->product_name ?: ($item->sku?->product?->name ?? ''),
                is_array($item->sku_specs) ? implode(', ', $item->sku_specs) : ($item->sku_specs ?? ''),
                $item->quantity,
                $item->price,
                $item->subtotal,
            ];

            if ($this->exportStrategy) {
                $row[] = $item->strategy_price ?: 0;
            }

            if ($this->exportActual) {
                $row[] = $item->actual_quantity ?: '';
                $row[] = $item->actual_subtotal ?: 0;
            }

            return $row;
        })->toArray();

        // 金额列从厘转为元
        $moneyColIndices = [];
        foreach ($moneyColumns as $mc) {
            $idx = array_search($mc, $columns);
            if ($idx !== false) {
                $moneyColIndices[] = $idx;
            }
        }

        foreach ($data as &$row) {
            foreach ($moneyColIndices as $idx) {
                if (isset($row[$idx]) && is_numeric($row[$idx])) {
                    $row[$idx] = round($row[$idx] / 1000, 2);
                }
            }
        }
        unset($row);

        $fileName = '订单明细_' . $this->order->order_no . '_' . now()->format('Ymd_His') . '.xlsx';

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
            $skipped = 0;

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // 跳过标题行

                $skuCode = trim($row[0] ?? '');
                $quantity = (int) ($row[1] ?? 0);
                $price = money_to_cents($row[2] ?? 0);

                if (! $skuCode || $quantity <= 0) {
                    $skipped++;
                    continue;
                }

                $sku = Sku::where('sku_code', $skuCode)->first();
                if (! $sku) {
                    $skipped++;
                    continue;
                }

                // 检查是否已存在同一SKU
                $exists = OrderItem::where('order_id', $this->order->id)
                    ->where('sku_id', $sku->id)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                try {
                    $subtotal = $price * $quantity;
                    OrderItem::create([
                        'order_id' => $this->order->id,
                        'sku_id' => $sku->id,
                        'product_name' => $sku->product?->name ?? '',
                        'sku_specs' => $sku->specs ?? null,
                        'quantity' => $quantity,
                        'price' => $price,
                        'actual_quantity' => $quantity,
                        'actual_price' => $price,
                        'subtotal' => $subtotal,
                        'actual_subtotal' => $subtotal,
                        'strategy_price' => 0,
                        'strategy_amount' => 0,
                        'discrepancy_amount' => 0,
                        'status' => OrderItem::STATUS_NORMAL,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $skipped++;
                    continue;
                }
            }

            $this->recalculateTotal();
            $this->loadOrder($this->order->id);
            $this->showImportModal = false;

            $msg = "成功导入 {$imported} 条明细";
            if ($skipped > 0) {
                $msg .= "，跳过 {$skipped} 条（SKU不存在/已存在/数据无效）";
            }
            $this->toastSuccess($msg);
        } catch (\Exception $e) {
            $this->toastError('导入失败：' . $e->getMessage());
        }
    }

    // ── 内部方法 ──

    private function resetAddItemForm(): void
    {
        $this->editingItemId = null;
        $this->formSkuId = 0;
        $this->formQuantity = 0;
        $this->formPrice = '';
        $this->formRemark = '';
    }

    private function recalculateTotal(): void
    {
        $total = OrderItem::where('order_id', $this->order->id)->sum('subtotal');
        $this->order->update([
            'total_amount' => $total,
            'adjusted_amount' => $total,
            'final_amount' => $total,
        ]);
    }

    public function render()
    {
        $skus = Sku::with('product')->orderBy('sku_code')->get();
        $isSuperAdmin = $this->isSuperAdmin;
        $canEditItems = $this->canEditItems;

        return view('livewire.order.order-detail', compact('skus', 'isSuperAdmin', 'canEditItems'))
            ->layout('components.app-layout')
            ->title("订单 {$this->order->order_no}");
    }
}
