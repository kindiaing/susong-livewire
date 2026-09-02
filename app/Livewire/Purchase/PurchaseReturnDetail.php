<?php

namespace App\Livewire\Purchase;

use App\Livewire\Traits\WithToast;
use App\Models\AuditLog;
use App\Models\PurchaseReturn;
use App\Models\Sku;
use App\Services\PurchaseReturnService;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseReturnDetail extends Component
{
    use WithToast;

    public PurchaseReturn $return;

    public $items;

    /**
     * 可编辑明细行（仅待审核状态）
     * 关联模式：[{purchase_order_item_id, sku_id, sku_name, max_quantity, quantity, price(厘), reason}]
     * 普通模式：[{sku_id, quantity, price_yuan(元), reason}]
     */
    public array $formItems = [];

    // 明细导入文件（普通模式）
    public $itemsImportFile = null;

    // 状态操作确认
    public bool $showConfirmModal = false;

    public string $confirmAction = '';

    public string $confirmTitle = '';

    public function mount(int $id): void
    {
        $this->loadReturn($id);
    }

    public function getIsSuperAdminProperty(): bool
    {
        return can_rollback_status();
    }

    public function getIsLinkedProperty(): bool
    {
        return $this->return->return_type === PurchaseReturn::TYPE_LINKED;
    }

    public function loadReturn(int $id): void
    {
        $this->return = PurchaseReturn::with(['purchaseOrder.items.sku.product', 'supplier', 'warehouse', 'operator', 'auditor', 'items.sku.product', 'auditLogs.operator'])->findOrFail($id);
        $this->items = $this->return->items;

        // 待审核状态同步可编辑明细行
        $this->formItems = [];
        if ($this->return->status === PurchaseReturn::STATUS_PENDING) {
            foreach ($this->return->items as $item) {
                if ($this->isLinked) {
                    $orderItem = $this->return->purchaseOrder?->items->firstWhere('id', $item->purchase_order_item_id);
                    $this->formItems[] = [
                        'purchase_order_item_id' => $item->purchase_order_item_id,
                        'sku_id' => $item->sku_id,
                        'sku_name' => ($item->sku?->product?->name ?? 'SKU').'（'.$item->sku?->sku_code.'）',
                        'max_quantity' => (int) ($orderItem?->actual_quantity ?? $item->quantity),
                        'quantity' => (int) $item->quantity,
                        'price' => (int) $item->price,
                        'reason' => $item->reason ?? '',
                    ];
                } else {
                    $this->formItems[] = [
                        'sku_id' => $item->sku_id,
                        'quantity' => (string) (int) $item->quantity,
                        'price_yuan' => number_format($item->price / 1000, 2, '.', ''),
                        'reason' => $item->reason ?? '',
                    ];
                }
            }
        }
    }

    // ── 明细编辑（仅待审核状态） ──

    /**
     * 新增一行明细
     */
    public function addItemRow(): void
    {
        if ($this->isLinked) {
            $this->formItems[] = [
                'purchase_order_item_id' => 0,
                'sku_id' => 0,
                'sku_name' => '',
                'max_quantity' => 0,
                'quantity' => '',
                'price' => 0,
                'reason' => '',
            ];
        } else {
            $this->formItems[] = [
                'sku_id' => 0,
                'quantity' => '',
                'price_yuan' => '',
                'reason' => '',
            ];
        }
    }

    public function removeItemRow(int $index): void
    {
        unset($this->formItems[$index]);
        $this->formItems = array_values($this->formItems);
    }

    /**
     * 关联模式：选择采购单明细行，带出 SKU / 可退上限 / 单价
     */
    public function pickOrderItem(int $index, $orderItemId): void
    {
        $orderItem = $this->return->purchaseOrder?->items->firstWhere('id', (int) $orderItemId);
        if (! $orderItem || ! isset($this->formItems[$index])) {
            return;
        }

        $this->formItems[$index]['purchase_order_item_id'] = (int) $orderItem->id;
        $this->formItems[$index]['sku_id'] = (int) $orderItem->sku_id;
        $this->formItems[$index]['sku_name'] = ($orderItem->sku?->product?->name ?? 'SKU').'（'.$orderItem->sku?->sku_code.'）';
        $this->formItems[$index]['max_quantity'] = (int) $orderItem->actual_quantity;
        $this->formItems[$index]['quantity'] = (int) $orderItem->actual_quantity;
        $this->formItems[$index]['price'] = (int) ($orderItem->actual_price ?: $orderItem->price);
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
        $isLinked = $this->isLinked;

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

    /**
     * 保存明细（整单替换，服务端校验数量上限与状态）
     */
    public function saveItems(): void
    {
        $payload = [];
        $seenOrderItems = [];

        foreach ($this->formItems as $row) {
            if ($this->isLinked) {
                $orderItemId = (int) ($row['purchase_order_item_id'] ?? 0);
                $qty = (int) ($row['quantity'] ?? 0);
                if (! $orderItemId || $qty <= 0) {
                    continue;
                }
                if (isset($seenOrderItems[$orderItemId])) {
                    $this->toastError('同一采购明细行重复，请合并为一行');

                    return;
                }
                $seenOrderItems[$orderItemId] = true;
                $payload[] = [
                    'purchase_order_item_id' => $orderItemId,
                    'quantity' => $qty,
                    'reason' => ($row['reason'] ?? '') ?: null,
                ];
            } else {
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
        }

        try {
            app(PurchaseReturnService::class)->updateItems($this->return, $payload);
            $this->loadReturn($this->return->id);
            $this->toastSuccess('退货明细已保存（合计 '.money_format($this->return->total_amount).'）');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // ── 状态流转 ──

    public function confirmApprove(): void
    {
        $this->confirmAction = 'approve';
        $this->confirmTitle = '确认审核通过此退货单？';
        $this->showConfirmModal = true;
    }

    public function confirmShip(): void
    {
        $this->confirmAction = 'ship';
        $this->confirmTitle = '确认标记为已出库？出库将扣减库存。';
        $this->showConfirmModal = true;
    }

    public function confirmComplete(): void
    {
        $this->confirmAction = 'complete';
        $this->confirmTitle = '确认完成此退货单？';
        $this->showConfirmModal = true;
    }

    public function confirmCancel(): void
    {
        $this->confirmAction = 'cancel';
        $this->confirmTitle = '确认作废此退货单？此操作不可撤销。';
        $this->showConfirmModal = true;
    }

    // ── 超管状态回退 ──

    public function confirmRollback(string $toStatus): void
    {
        $statusLabels = PurchaseReturn::statusMap();
        $this->confirmAction = 'rollback_'.$toStatus;
        $this->confirmTitle = "确认回退到【{$statusLabels[$toStatus]}】状态？";
        $this->showConfirmModal = true;
    }

    public function executeConfirm(): void
    {
        try {
            $service = app(PurchaseReturnService::class);

            // 超管状态回退
            if (str_starts_with($this->confirmAction, 'rollback_')) {
                abort_unless(can_rollback_status(), 403, '仅超级管理员可执行状态回退');

                $toStatus = (int) substr($this->confirmAction, 9);
                $oldStatus = $this->return->status;
                $this->return->update(['status' => $toStatus]);

                AuditLog::log(
                    modelType: PurchaseReturn::class,
                    modelId: $this->return->id,
                    action: 'rollback',
                    beforeData: ['status' => $oldStatus],
                    afterData: ['status' => $toStatus],
                );

                $this->loadReturn($this->return->id);
                $this->showConfirmModal = false;
                $this->toastSuccess('状态已回退');

                return;
            }

            match ($this->confirmAction) {
                'approve' => $service->approve($this->return),
                'ship' => $service->ship($this->return),
                'complete' => $service->complete($this->return),
                'cancel' => $service->cancel($this->return),
                default => null,
            };

            $this->loadReturn($this->return->id);
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

    public function render()
    {
        $isSuperAdmin = $this->isSuperAdmin;
        // 普通模式：SKU 下拉选项；关联模式：采购单可退明细选项（实际入库>0）
        $skus = Sku::with('product')->where('status', 1)->orderBy('id', 'desc')->limit(200)->get();
        $orderItems = $this->isLinked
            ? ($this->return->purchaseOrder?->items ?? collect())->filter(fn ($oi) => $oi->actual_quantity > 0)->values()
            : collect();

        return view('livewire.purchase.purchase-return-detail', compact('isSuperAdmin', 'skus', 'orderItems'))
            ->layout('components.app-layout')
            ->title("退货单 {$this->return->return_no}");
    }
}
