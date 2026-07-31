<?php

namespace App\Livewire\Traits;

use App\Models\UserPreference;

/**
 * 列表页：自定义列显示 Trait
 * - 支持显示/隐藏列
 * - 偏好持久化到数据库（user_preferences 表），按用户隔离
 * - 未登录时降级到 session
 */
trait WithColumnVisibility
{
    public bool $showColumnModal = false;

    public array $visibleColumns = [];

    /**
     * 初始化列可见性（组件 mount 中调用）
     */
    public function initColumnVisibility(): void
    {
        $key = $this->getColumnVisibilityKey();
        $userId = $this->getPreferenceUserId();

        if ($userId) {
            $saved = UserPreference::getPreference($userId, "col_vis.{$key}");
        } else {
            $saved = session()->get("col_vis.{$key}");
        }

        $this->visibleColumns = $saved ?? $this->getDefaultColumns();
    }

    public function openColumnModal(): void
    {
        $this->showColumnModal = true;
    }

    public function toggleColumn(string $column): void
    {
        $idx = array_search($column, $this->visibleColumns);
        if ($idx !== false) {
            unset($this->visibleColumns[$idx]);
            $this->visibleColumns = array_values($this->visibleColumns);
        } else {
            $this->visibleColumns[] = $column;
        }
        $this->saveColumnVisibility();
    }

    public function resetColumns(): void
    {
        $this->visibleColumns = $this->getDefaultColumns();
        $this->saveColumnVisibility();
    }

    public function selectAllColumns(): void
    {
        $this->visibleColumns = collect($this->getAllColumns())->pluck('key')->toArray();
        $this->saveColumnVisibility();
    }

    public function closeColumnModal(): void
    {
        $this->showColumnModal = false;
    }

    public function isColumnVisible(string $column): bool
    {
        return in_array($column, $this->visibleColumns);
    }

    private function saveColumnVisibility(): void
    {
        $key = $this->getColumnVisibilityKey();
        $userId = $this->getPreferenceUserId();

        if ($userId) {
            UserPreference::setPreference($userId, "col_vis.{$key}", $this->visibleColumns);
        } else {
            session()->put("col_vis.{$key}", $this->visibleColumns);
        }
    }

    /**
     * 获取当前用户 ID（未登录时返回 null，降级到 session）
     */
    protected function getPreferenceUserId(): ?int
    {
        return auth()->id();
    }

    /**
     * 组件覆盖：唯一标识（默认用类名）
     */
    public function getColumnVisibilityKey(): string
    {
        return str_replace('\\', '_', static::class);
    }

    /**
     * 组件覆盖：默认显示的列 key 数组
     */
    public function getDefaultColumns(): array
    {
        return collect($this->getAllColumns())->pluck('key')->toArray();
    }

    /**
     * 组件覆盖：所有列定义
     * 格式：[['key'=>'id','label'=>'ID','sortable'=>true,'exportable'=>true,'width'=>'60px'], ...]
     */
    public function getAllColumns(): array
    {
        return [];
    }
}
