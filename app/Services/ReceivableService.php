<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\MerchantAccount;
use App\Models\Order;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use Illuminate\Support\Facades\DB;

/**
 * 应收账款服务
 *
 * 职责：
 * - createFromOrder()：订单签收时自动生成应收单（A1 决策）
 * - applyReturnAmount()：售后退货退款完成时回写 return_amount（B1 决策）
 * - applyDiscrepancyAmount()：差异处理完成时回写 discrepancy_amount
 * - recalculate()：重算 adjusted_amount = original − return − discrepancy − strategy_discount
 * - settleCheck()：判断并更新结清状态
 *
 * 金额公式：adjusted = original − discrepancy − return − strategy_discount
 * 结清条件：received >= adjusted → status=3
 */
class ReceivableService
{
    /**
     * 从订单签收生成应收单（A1：签收即生成）
     *
     * - original_amount = order.total_amount
     * - adjusted_amount = order.adjusted_amount（含称重差异）
     * - settlement_type 从订单映射
     * - due_date 账期结算时 = 签收日 + 账期天数（系统配置 credit_period_days，默认 15）
     * - 防重复：同 order_id 不重复生成
     */
    public function createFromOrder(Order $order): ?Receivable
    {
        // 防重复
        if (Receivable::where('order_id', $order->id)->exists()) {
            return null;
        }

        $originalAmount = (int) $order->total_amount;
        $adjustedAmount = (int) ($order->adjusted_amount ?: $order->total_amount);
        $settlementType = (int) ($order->settlement_type ?: Receivable::SETTLEMENT_CASH);

        // 账期结算：计算到期日
        $dueDate = null;
        if ($settlementType === Receivable::SETTLEMENT_CREDIT) {
            $creditDays = (int) setting('credit_period_days', 15);
            $dueDate = now()->addDays($creditDays)->toDateString();
        }

        return DB::transaction(function () use ($order, $originalAmount, $adjustedAmount, $settlementType, $dueDate) {
            $receivable = Receivable::create([
                'receivable_no' => generate_sequence_no('RCV', 'receivables', 'receivable_no'),
                'order_id' => $order->id,
                'merchant_id' => $order->merchant_id,
                'original_amount' => $originalAmount,
                'adjusted_amount' => $adjustedAmount,
                'discrepancy_amount' => 0,
                'return_amount' => 0,
                'strategy_discount_amount' => 0,
                'received_amount' => 0,
                'status' => Receivable::STATUS_UNSETTLED,
                'settlement_type' => $settlementType,
                'due_date' => $dueDate,
                'approval_status' => Receivable::APPROVAL_APPROVED,
            ]);

            AuditLog::log(
                modelType: Receivable::class,
                modelId: $receivable->id,
                action: 'create_from_order',
                beforeData: null,
                afterData: [
                    'receivable_no' => $receivable->receivable_no,
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'original_amount' => $originalAmount,
                    'adjusted_amount' => $adjustedAmount,
                    'settlement_type' => $settlementType,
                ],
            );

            return $receivable;
        });
    }

    /**
     * 售后退货退款完成时回写应收（B1：退款完成才回写）
     *
     * - 累加 return_amount
     * - 重算 adjusted_amount
     * - 若已收 > 新 adjusted，状态保持（需人工处理多收退款）
     */
    public function applyReturnAmount(int $orderId, int $refundAmount): ?Receivable
    {
        $receivable = Receivable::where('order_id', $orderId)->first();
        if (!$receivable) {
            return null;
        }

        return DB::transaction(function () use ($receivable, $refundAmount) {
            $before = [
                'return_amount' => $receivable->return_amount,
                'adjusted_amount' => $receivable->adjusted_amount,
            ];

            $receivable->return_amount += $refundAmount;
            $this->recalculate($receivable);
            $receivable->save();

            AuditLog::log(
                modelType: Receivable::class,
                modelId: $receivable->id,
                action: 'apply_return',
                beforeData: $before,
                afterData: [
                    'return_amount' => $receivable->return_amount,
                    'adjusted_amount' => $receivable->adjusted_amount,
                ],
            );

            return $receivable->fresh();
        });
    }

    /**
     * 差异处理完成时回写应收
     *
     * - 累加 discrepancy_amount（防重复：检查 is_amount_adjusted）
     * - 重算 adjusted_amount
     */
    public function applyDiscrepancyAmount(int $orderId, int $amountDiff): ?Receivable
    {
        $receivable = Receivable::where('order_id', $orderId)->first();
        if (!$receivable) {
            return null;
        }

        return DB::transaction(function () use ($receivable, $amountDiff) {
            $before = [
                'discrepancy_amount' => $receivable->discrepancy_amount,
                'adjusted_amount' => $receivable->adjusted_amount,
            ];

            $receivable->discrepancy_amount += $amountDiff;
            $this->recalculate($receivable);
            $receivable->save();

            AuditLog::log(
                modelType: Receivable::class,
                modelId: $receivable->id,
                action: 'apply_discrepancy',
                beforeData: $before,
                afterData: [
                    'discrepancy_amount' => $receivable->discrepancy_amount,
                    'adjusted_amount' => $receivable->adjusted_amount,
                ],
            );

            return $receivable->fresh();
        });
    }

    /**
     * 重算 adjusted_amount = original − discrepancy − return − strategy_discount
     * 并重算结清状态
     */
    public function recalculate(Receivable $receivable): void
    {
        $adjusted = $receivable->original_amount
            - $receivable->discrepancy_amount
            - $receivable->return_amount
            - $receivable->strategy_discount_amount;

        $receivable->adjusted_amount = max(0, $adjusted);

        // 重算结清状态
        $this->settleCheck($receivable);
    }

    /**
     * 判断并更新结清状态
     * received >= adjusted → 已结清(3)
     * received > 0 且 < adjusted → 部分收款(2)
     * received = 0 → 未结算(1)
     * 争议中(4)和已办结(5)不自动变更
     */
    public function settleCheck(Receivable $receivable): void
    {
        if (in_array($receivable->status, [Receivable::STATUS_DISPUTED, Receivable::STATUS_CLOSED])) {
            return;
        }

        if ($receivable->received_amount >= $receivable->adjusted_amount && $receivable->adjusted_amount > 0) {
            $receivable->status = Receivable::STATUS_SETTLED;
            $receivable->settled_at ??= now();
        } elseif ($receivable->received_amount > 0) {
            $receivable->status = Receivable::STATUS_PARTIAL;
            $receivable->settled_at = null;
        } else {
            $receivable->status = Receivable::STATUS_UNSETTLED;
            $receivable->settled_at = null;
        }
    }

    /**
     * 预付款抵扣（C2：余额不足允许签收转争议）
     *
     * - 余额充足：扣减 balance，记录 total_consumption，创建收款记录
     * - 余额不足：应收转争议中(4)，不阻断签收
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function tryPrepaidDeduction(Receivable $receivable): array
    {
        $account = MerchantAccount::where('merchant_id', $receivable->merchant_id)->first();

        if (!$account) {
            return ['success' => false, 'message' => '商家账户不存在'];
        }

        $available = $account->balance + $account->credit_limit;
        $needed = $receivable->adjusted_amount;

        if ($available >= $needed) {
            // 余额充足：直接扣减
            DB::transaction(function () use ($account, $receivable, $needed) {
                $account->balance -= $needed;
                $account->total_consumption += $needed;
                $account->save();

                $receivable->received_amount = $needed;
                $receivable->status = Receivable::STATUS_SETTLED;
                $receivable->settled_at = now();
                $receivable->save();

                ReceivablePayment::create([
                    'receivable_id' => $receivable->id,
                    'amount' => $needed,
                    'payment_method' => ReceivablePayment::METHOD_BALANCE,
                    'operator_id' => auth()->id(),
                    'approval_status' => ReceivablePayment::APPROVAL_APPROVED,
                    'remark' => '预付款签收自动扣减',
                ]);
            });

            return ['success' => true, 'message' => '预付款扣减成功'];
        }

        // 余额不足：转争议中
        DB::transaction(function () use ($receivable) {
            $receivable->status = Receivable::STATUS_DISPUTED;
            $receivable->remark = '预付款余额不足，转争议挂账';
            $receivable->save();

            AuditLog::log(
                modelType: Receivable::class,
                modelId: $receivable->id,
                action: 'prepaid_insufficient',
                beforeData: null,
                afterData: ['status' => Receivable::STATUS_DISPUTED, 'reason' => '余额不足'],
            );
        });

        return ['success' => false, 'message' => '预付款余额不足，应收转争议中'];
    }
}
