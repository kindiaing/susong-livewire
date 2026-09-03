<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 售后退货服务
 *
 * 封装售后退货完整生命周期：创建（头先行）→ 明细维护 → 审核 → 退货 → 退款 / 作废
 * 与采购退货同一交互模式：空头建单，详情页维护明细，审核时校验明细非空。
 * 数量上限 = 订单明细实收数量（actual_quantity ?: quantity），退的货必须是客户实际收到的。
 */
class OrderReturnService
{
    /**
     * 创建售后退货单（头先行，明细可为空，详情页补录）
     *
     * @param  array  $items  退货明细 [{order_item_id, quantity, reason}]，可为空
     *
     * @throws \Exception
     */
    public function create(Order $order, array $items, string $reason = ''): OrderReturn
    {
        // 仅已签收/已锁定的订单可发起售后退货
        if (! in_array($order->status, [Order::STATUS_SIGNED, Order::STATUS_LOCKED])) {
            throw new \Exception('仅已签收的订单可创建售后退货');
        }

        return DB::transaction(function () use ($order, $items, $reason) {
            $return = OrderReturn::create([
                'return_no' => generate_sequence_no('RT', 'order_returns', 'return_no'),
                'order_id' => $order->id,
                'merchant_id' => $order->merchant_id,
                'status' => OrderReturn::STATUS_PENDING,
                'total_amount' => 0,
                'refund_amount' => 0,
                'reason' => $reason,
                'operator_id' => Auth::id(),
            ]);

            foreach ($items as $item) {
                $this->createItem($return, $order, $item);
            }

            $this->recalculateAmounts($return);

            return $return->fresh();
        });
    }

    /**
     * 保存退货明细（待审核状态整单替换：先删后建）
     *
     * @param  array  $items  [{order_item_id, quantity, reason}]
     *
     * @throws \Exception
     */
    public function updateItems(OrderReturn $return, array $items): OrderReturn
    {
        if ($return->status !== OrderReturn::STATUS_PENDING) {
            throw new \Exception('仅待审核状态的退货单可编辑明细，需管理员回退状态后才能修改');
        }

        return DB::transaction(function () use ($return, $items) {
            $return->items()->delete();

            foreach ($items as $item) {
                $this->createItem($return, $return->order, $item);
            }

            $this->recalculateAmounts($return);

            return $return->fresh();
        });
    }

    /**
     * 创建一行退货明细（数量上限=订单实收数量）
     *
     * @throws \Exception
     */
    private function createItem(OrderReturn $return, Order $order, array $item): void
    {
        /** @var OrderItem $orderItem */
        $orderItem = $order->items()->findOrFail($item['order_item_id']);
        $quantity = (int) $item['quantity'];

        if ($quantity <= 0) {
            throw new \Exception('退货数量必须大于0');
        }

        // 退货数量不能大于客户实收数量（退的货必须是实际收到的）
        $maxQuantity = (int) ($orderItem->actual_quantity ?: $orderItem->quantity);
        if ($quantity > $maxQuantity) {
            throw new \Exception("退货数量({$quantity})不能大于实收数量({$maxQuantity})");
        }

        $price = (int) ($orderItem->actual_price ?: $orderItem->price);

        OrderReturnItem::create([
            'order_return_id' => $return->id,
            'order_item_id' => $orderItem->id,
            'sku_id' => $orderItem->sku_id,
            'quantity' => $quantity,
            'price' => $price,
            'amount' => $quantity * $price,
            'refund_amount' => 0,
            'reason' => $item['reason'] ?? null,
        ]);
    }

    /**
     * 审核通过（待审核→已审核）
     *
     * @throws \Exception
     */
    public function approve(OrderReturn $return): OrderReturn
    {
        if ($return->status !== OrderReturn::STATUS_PENDING) {
            throw new \Exception('仅待审核状态的退货单可审核');
        }

        if ($return->items()->count() === 0) {
            throw new \Exception('请先添加退货明细再审核');
        }

        $oldStatus = $return->status;

        $return->update([
            'status' => OrderReturn::STATUS_APPROVED,
            'audited_by' => Auth::id(),
            'audited_at' => now(),
        ]);

        $this->auditStatusChange($return, 'approve', $oldStatus, OrderReturn::STATUS_APPROVED);

        return $return->fresh();
    }

    /**
     * 确认退货（已审核→已退货）
     *
     * @throws \Exception
     */
    public function markReturned(OrderReturn $return): OrderReturn
    {
        if ($return->status !== OrderReturn::STATUS_APPROVED) {
            throw new \Exception('仅已审核状态的退货单可确认退货');
        }

        $return->update([
            'status' => OrderReturn::STATUS_RETURNED,
            'returned_at' => now(),
        ]);

        $this->auditStatusChange($return, 'return', OrderReturn::STATUS_APPROVED, OrderReturn::STATUS_RETURNED);

        return $return->fresh();
    }

    /**
     * 完成退款（已退货→退款完成）
     * 退款金额 = 退货总金额，明细 refund_amount 同步为各行退货金额
     *
     * @throws \Exception
     */
    public function refund(OrderReturn $return): OrderReturn
    {
        if ($return->status !== OrderReturn::STATUS_RETURNED) {
            throw new \Exception('仅已退货状态的退货单可完成退款');
        }

        return DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                $item->update(['refund_amount' => $item->amount]);
            }

            $return->update([
                'status' => OrderReturn::STATUS_REFUNDED,
                'refund_amount' => $return->total_amount,
                'refunded_at' => now(),
            ]);

            $this->auditStatusChange($return, 'refund', OrderReturn::STATUS_RETURNED, OrderReturn::STATUS_REFUNDED);

            // 退款完成回写应收（B1 决策）
            try {
                app(ReceivableService::class)->applyReturnAmount(
                    $return->order_id,
                    (int) $return->refund_amount,
                );
            } catch (\Exception $e) {
                Log::warning('应收回写失败（退货退款）', ['order_id' => $return->order_id, 'error' => $e->getMessage()]);
            }

            return $return->fresh();
        });
    }

    /**
     * 作废退货（待审核/已审核可作废）
     *
     * @throws \Exception
     */
    public function cancel(OrderReturn $return, ?string $reason = null): OrderReturn
    {
        if (! in_array($return->status, [OrderReturn::STATUS_PENDING, OrderReturn::STATUS_APPROVED])) {
            throw new \Exception('仅待审核/已审核状态的退货单可作废');
        }

        $oldStatus = $return->status;

        $return->update([
            'status' => OrderReturn::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        $this->auditStatusChange($return, 'cancel', $oldStatus, OrderReturn::STATUS_CANCELLED, $reason);

        return $return->fresh();
    }

    /**
     * 重算退货总金额
     */
    private function recalculateAmounts(OrderReturn $return): void
    {
        $return->total_amount = $return->items()->sum('amount');
        $return->save();
    }

    /**
     * 记录退货单状态变更审计日志
     */
    private function auditStatusChange(OrderReturn $return, string $action, int $oldStatus, int $newStatus, ?string $reason = null): void
    {
        if (! setting('audit_order_return', true)) {
            return;
        }

        AuditLog::log(
            modelType: OrderReturn::class,
            modelId: $return->id,
            action: $action,
            beforeData: ['status' => $oldStatus, 'status_label' => OrderReturn::statusMap()[$oldStatus] ?? '未知'],
            afterData: ['status' => $newStatus, 'status_label' => OrderReturn::statusMap()[$newStatus] ?? '未知'],
            reason: $reason,
            relationType: 'order_return',
            relationId: $return->id,
        );
    }
}
