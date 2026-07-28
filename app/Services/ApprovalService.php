<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalTypeConfig;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * 审批流程服务
 * 统一处理审批的创建、审核、撤回等操作
 */
class ApprovalService
{
    /**
     * 创建审批申请
     * 如果该类型审核已关闭，直接返回 null（表示无需审批，业务可继续执行）
     *
     * @return Approval|null 返回审批记录，或 null（无需审批）
     */
    public static function createRequest(
        string $typeCode,
        string $targetType,
        int $targetId,
        ?array $beforeData = null,
        ?array $afterData = null,
        ?int $amount = null,
        ?string $reason = null,
    ): ?Approval {
        // 检查是否需要审核
        if (!ApprovalTypeConfig::isApprovalRequired($typeCode)) {
            return null;
        }

        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // 检查是否已有相同的待审核记录（防重复提交）
        $existing = Approval::where('approval_type', $typeCode)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('status', Approval::STATUS_PENDING)
            ->first();

        if ($existing) {
            return $existing;
        }

        $approval = Approval::create([
            'approval_type' => $typeCode,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'applicant_id' => $user->id,
            'applicant_name' => $user->name ?? $user->username,
            'before_data' => $beforeData,
            'after_data' => $afterData,
            'amount' => $amount,
            'status' => Approval::STATUS_PENDING,
        ]);

        // 记录审计日志
        AuditLog::log(
            modelType: 'Approval',
            modelId: $approval->id,
            action: 'create',
            afterData: $approval->toArray(),
            reason: $reason,
        );

        return $approval;
    }

    /**
     * 审核通过
     */
    public static function approve(Approval $approval, ?string $remark = null): bool
    {
        if ($approval->status !== Approval::STATUS_PENDING) {
            return false;
        }

        $user = Auth::user();
        $beforeStatus = $approval->status;

        $approval->update([
            'status' => Approval::STATUS_APPROVED,
            'reviewer_id' => $user->id,
            'reviewer_name' => $user->name ?? $user->username,
            'review_remark' => $remark,
            'reviewed_at' => now(),
        ]);

        // 记录审计日志
        AuditLog::log(
            modelType: 'Approval',
            modelId: $approval->id,
            action: 'approve',
            beforeData: ['status' => $beforeStatus],
            afterData: ['status' => Approval::STATUS_APPROVED, 'reviewer' => $user->name ?? $user->username],
            reason: $remark,
        );

        return true;
    }

    /**
     * 审核拒绝
     */
    public static function reject(Approval $approval, ?string $remark = null): bool
    {
        if ($approval->status !== Approval::STATUS_PENDING) {
            return false;
        }

        $user = Auth::user();
        $beforeStatus = $approval->status;

        $approval->update([
            'status' => Approval::STATUS_REJECTED,
            'reviewer_id' => $user->id,
            'reviewer_name' => $user->name ?? $user->username,
            'review_remark' => $remark,
            'reviewed_at' => now(),
        ]);

        // 记录审计日志
        AuditLog::log(
            modelType: 'Approval',
            modelId: $approval->id,
            action: 'reject',
            beforeData: ['status' => $beforeStatus],
            afterData: ['status' => Approval::STATUS_REJECTED, 'reviewer' => $user->name ?? $user->username],
            reason: $remark,
        );

        return true;
    }

    /**
     * 撤回审批（仅申请人可撤回，且仅限待审核状态）
     */
    public static function withdraw(Approval $approval): bool
    {
        $user = Auth::user();

        if ($approval->status !== Approval::STATUS_PENDING) {
            return false;
        }

        if ($approval->applicant_id !== $user->id) {
            return false;
        }

        $approval->update([
            'status' => Approval::STATUS_WITHDRAWN,
        ]);

        AuditLog::log(
            modelType: 'Approval',
            modelId: $approval->id,
            action: 'update',
            afterData: ['status' => Approval::STATUS_WITHDRAWN],
            reason: '申请人主动撤回',
        );

        return true;
    }

    /**
     * 检查审批是否已通过
     * 用于业务执行前判断
     */
    public static function isApproved(string $typeCode, string $targetType, int $targetId): bool
    {
        // 该类型不需要审核，视为已通过
        if (!ApprovalTypeConfig::isApprovalRequired($typeCode)) {
            return true;
        }

        return Approval::where('approval_type', $typeCode)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('status', Approval::STATUS_APPROVED)
            ->exists();
    }

    /**
     * 获取待审核数量（按审核人角色）
     */
    public static function getPendingCountForUser(): int
    {
        $user = Auth::user();
        if (!$user) {
            return 0;
        }

        // 获取当前用户角色对应的审核类型
        $userRoleIds = $user->roles->pluck('id')->toArray();

        $reviewableTypes = ApprovalTypeConfig::enabled()
            ->whereIn('reviewer_role_id', $userRoleIds)
            ->pluck('type_code');

        if ($reviewableTypes->isEmpty()) {
            return 0;
        }

        return Approval::pending()
            ->whereIn('approval_type', $reviewableTypes)
            ->count();
    }
}
