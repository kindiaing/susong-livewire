<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\User;

/**
 * 通知服务
 *
 * 统一管理通知的创建与广播，支持三种发送方式：
 * - 指定用户（user_id）
 * - 指定商家（merchant_id）
 * - 全站广播（user_id=null, merchant_id=null）
 */
class NotificationService
{
    /**
     * 发送通知给指定用户
     */
    public function toUser(int $userId, int $type, string $title, ?string $content = null, ?array $data = null): Notification
    {
        return $this->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'data' => $data,
        ]);
    }

    /**
     * 发送通知给指定商家
     */
    public function toMerchant(int $merchantId, int $type, string $title, ?string $content = null, ?array $data = null): Notification
    {
        return $this->create([
            'merchant_id' => $merchantId,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'data' => $data,
        ]);
    }

    /**
     * 发送全站广播通知
     */
    public function broadcast(int $type, string $title, ?string $content = null, ?array $data = null): Notification
    {
        return $this->create([
            'user_id' => null,
            'merchant_id' => null,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'data' => $data,
        ]);
    }

    /**
     * 发送通知给拥有指定角色的所有用户
     */
    public function toRole(string $roleName, int $type, string $title, ?string $content = null, ?array $data = null): array
    {
        $users = User::whereHas('roles', fn ($q) => $q->where('name', $roleName))->get();
        $notifications = [];

        foreach ($users as $user) {
            $notifications[] = $this->toUser($user->id, $type, $title, $content, $data);
        }

        return $notifications;
    }

    /**
     * 发送通知给多个用户
     */
    public function toUsers(array $userIds, int $type, string $title, ?string $content = null, ?array $data = null): array
    {
        $notifications = [];

        foreach ($userIds as $userId) {
            $notifications[] = $this->toUser($userId, $type, $title, $content, $data);
        }

        return $notifications;
    }

    /**
     * 创建通知并触发广播
     */
    protected function create(array $params): Notification
    {
        $notification = Notification::create([
            'user_id' => $params['user_id'] ?? null,
            'merchant_id' => $params['merchant_id'] ?? null,
            'type' => $params['type'],
            'title' => $params['title'],
            'content' => $params['content'] ?? null,
            'data' => $params['data'] ?? null,
            'is_read' => 0,
        ]);

        // 触发广播事件
        try {
            broadcast(new NotificationCreated($notification))->toOthers();
        } catch (\Throwable $e) {
            // 广播失败不影响通知入库，记录日志即可
            logger()->warning('通知广播失败: ' . $e->getMessage(), [
                'notification_id' => $notification->id,
            ]);
        }

        return $notification;
    }

    /*
    |--------------------------------------------------------------------------
    | 便捷方法：按业务场景快速创建通知
    |--------------------------------------------------------------------------
    */

    /**
     * 订单状态变更通知
     */
    public function orderStatusChanged(int $merchantId, string $orderNo, string $fromStatus, string $toStatus): Notification
    {
        return $this->toMerchant($merchantId, Notification::TYPE_ORDER, '订单状态变更', "订单 {$orderNo} 状态从「{$fromStatus}」变更为「{$toStatus}」", [
            'order_no' => $orderNo,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
        ]);
    }

    /**
     * 充值审核通过通知
     */
    public function rechargeApproved(int $merchantId, string $rechargeNo, string $amount): Notification
    {
        return $this->toMerchant($merchantId, Notification::TYPE_ACCOUNT, '充值到账', "充值单 {$rechargeNo} 已审核通过，到账金额 ¥{$amount}", [
            'recharge_no' => $rechargeNo,
            'amount' => $amount,
        ]);
    }

    /**
     * 供应商结算完成通知
     */
    public function settlementCompleted(int $userId, string $settlementNo, string $amount): Notification
    {
        return $this->toUser($userId, Notification::TYPE_ACCOUNT, '结算完成', "供应商结算单 {$settlementNo} 已结清，金额 ¥{$amount}", [
            'settlement_no' => $settlementNo,
            'amount' => $amount,
        ]);
    }

    /**
     * 应收收款确认通知
     */
    public function receivableCollected(int $userId, string $receivableNo, string $amount): Notification
    {
        return $this->toUser($userId, Notification::TYPE_ACCOUNT, '收款确认', "应收单 {$receivableNo} 已收款，金额 ¥{$amount}", [
            'receivable_no' => $receivableNo,
            'amount' => $amount,
        ]);
    }

    /**
     * 库存预警通知
     */
    public function inventoryWarning(int $userId, string $skuName, int $currentStock, int $threshold): Notification
    {
        return $this->toUser($userId, Notification::TYPE_INVENTORY, '库存预警', "{$skuName} 当前库存 {$currentStock}，已低于预警阈值 {$threshold}", [
            'sku_name' => $skuName,
            'current_stock' => $currentStock,
            'threshold' => $threshold,
        ]);
    }

    /**
     * 采购单提交通知
     */
    public function purchaseSubmitted(int $userId, string $purchaseNo): Notification
    {
        return $this->toUser($userId, Notification::TYPE_SYSTEM, '采购单待审核', "采购单 {$purchaseNo} 已提交，请及时审核", [
            'purchase_no' => $purchaseNo,
        ]);
    }
}
