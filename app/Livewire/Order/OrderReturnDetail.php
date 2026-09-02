<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithToast;
use App\Models\AuditLog;
use App\Models\OrderReturn;
use App\Services\OrderReturnService;
use Livewire\Component;

class OrderReturnDetail extends Component
{
    use WithToast;

    public OrderReturn $return;

    public $items;

    /**
     * 可编辑明细行（仅待审核状态）
     * [{order_item_id, sku_id, sku_name, max_quantity, quantity, price(厘), reason}]
     */
    public array $formItems = [];

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

    public function loadReturn(int $id): void
    {
        $this->return = OrderReturn::with(['order.items.sku.product', 'merchant', 'operator', 'auditor', 'items.sku.product', 'auditLogs.operator'])->findOrFail($id);
        $this->items = $this->return->items;

        // 待审核状态同步可编辑明细行
        $this->formItems = [];
        if ($this->return->status === OrderReturn::STATUS_PENDING) {
            foreach ($this->return->items as $item) {
                $orderItem = $this->return->order?->items->firstWhere('id', $item->order_item_id);
                $this->formItems[] = [
                    'order_item_id' => $item->order_item_id,
                    'sku_id' => $item->sku_id,
                    'sku_name' => $item->sku?->product?->name ?? ($orderItem?->product_name ?? 'SKU'),
                    'max_quantity' => (int) ($orderItem ? ($orderItem->actual_quantity ?: $orderItem->quantity) : $item->quantity),
                    'quantity' => (int) $item->quantity,
                    'price' => (int) $item->price,
                    'reason' => $item->reason ?? '',
                ];
            }
        }
    }

    // ── 明细编辑（仅待审核状态） ──

    public function addItemRow(): void
    {
        $this->formItems[] = [
            'order_item_id' => 0,
            'sku_id' => 0,
            'sku_name' => '',
            'max_quantity' => 0,
            'quantity' => '',
            'price' => 0,
            'reason' => '',
        ];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->formItems[$index]);
        $this->formItems = array_values($this->formItems);
    }

    /**
     * 选择订单明细行，带出 SKU / 可退上限（实收数量）/ 单价
     */
    public function pickOrderItem(int $index, $orderItemId): void
    {
        $orderItem = $this->return->order?->items->firstWhere('id', (int) $orderItemId);
        if (! $orderItem || ! isset($this->formItems[$index])) {
            return;
        }

        $this->formItems[$index]['order_item_id'] = (int) $orderItem->id;
        $this->formItems[$index]['sku_id'] = (int) $orderItem->sku_id;
        $this->formItems[$index]['sku_name'] = $orderItem->sku?->product?->name ?? $orderItem->product_name ?? 'SKU';
        $this->formItems[$index]['max_quantity'] = (int) ($orderItem->actual_quantity ?: $orderItem->quantity);
        $this->formItems[$index]['quantity'] = $this->formItems[$index]['max_quantity'];
        $this->formItems[$index]['price'] = (int) ($orderItem->actual_price ?: $orderItem->price);
    }

    /**
     * 导出当前明细行 CSV
     */
    public function exportItems()
    {
        return response()->streamDownload(function () {
            echo "\xEF\xBB\xBF"."SKU编码,SKU名称,数量,单价(元),原因\n";
            foreach ($this->formItems as $row) {
                $orderItem = $this->return->order?->items->firstWhere('id', (int) ($row['order_item_id'] ?? 0));
                $code = $orderItem?->sku?->sku_code ?? '';
                $priceYuan = number_format(($row['price'] ?? 0) / 1000, 2, '.', '');
                echo implode(',', [$code, $row['sku_name'] ?? '', $row['quantity'] ?? '', $priceYuan, $row['reason'] ?? ''])."\n";
            }
        }, '售后退货明细_'.now()->format('Ymd_His').'.csv');
    }

    /**
     * 保存明细（整单替换，服务端校验状态与数量上限）
     */
    public function saveItems(): void
    {
        $payload = [];
        $seenOrderItems = [];

        foreach ($this->formItems as $row) {
            $orderItemId = (int) ($row['order_item_id'] ?? 0);
            $qty = (int) ($row['quantity'] ?? 0);
            if (! $orderItemId || $qty <= 0) {
                continue;
            }
            if (isset($seenOrderItems[$orderItemId])) {
                $this->toastError('同一订单明细行重复，请合并为一行');

                return;
            }
            $seenOrderItems[$orderItemId] = true;
            $payload[] = [
                'order_item_id' => $orderItemId,
                'quantity' => $qty,
                'reason' => ($row['reason'] ?? '') ?: null,
            ];
        }

        try {
            app(OrderReturnService::class)->updateItems($this->return, $payload);
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

    public function confirmReturn(): void
    {
        $this->confirmAction = 'return';
        $this->confirmTitle = '确认商品已退回？';
        $this->showConfirmModal = true;
    }

    public function confirmRefund(): void
    {
        $this->confirmAction = 'refund';
        $this->confirmTitle = '确认完成退款？退款金额 = 退货总金额。';
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
        $statusLabels = OrderReturn::statusMap();
        $this->confirmAction = 'rollback_'.$toStatus;
        $this->confirmTitle = "确认回退到【{$statusLabels[$toStatus]}】状态？";
        $this->showConfirmModal = true;
    }

    public function executeConfirm(): void
    {
        try {
            $service = app(OrderReturnService::class);

            // 超管状态回退
            if (str_starts_with($this->confirmAction, 'rollback_')) {
                abort_unless(can_rollback_status(), 403, '仅超级管理员可执行状态回退');

                $toStatus = (int) substr($this->confirmAction, 9);
                $oldStatus = $this->return->status;
                $this->return->update(['status' => $toStatus]);

                AuditLog::log(
                    modelType: OrderReturn::class,
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
                'return' => $service->markReturned($this->return),
                'refund' => $service->refund($this->return),
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
        // 可退订单明细选项（实收数量>0）
        $orderItems = ($this->return->order?->items ?? collect())
            ->filter(fn ($oi) => ($oi->actual_quantity ?: $oi->quantity) > 0)
            ->values();

        return view('livewire.order.order-return-detail', compact('isSuperAdmin', 'orderItems'))
            ->layout('components.app-layout')
            ->title("退货单 {$this->return->return_no}");
    }
}
