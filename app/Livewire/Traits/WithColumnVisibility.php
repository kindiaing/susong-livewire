<?php

namespace App\Livewire\Traits;

/**
 * 列表页：自定义列显示 Trait
 * - 支持显示/隐藏列
 * - 偏好存入 session
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
        $saved = session()->get("col_vis.{$key}");
        $this->visibleColumns = $saved ?? $this->getDefaultColumns();
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

    public function isColumnVisible(string $column): bool
    {
        return in_array($column, $this->visibleColumns);
    }

    private function saveColumnVisibility(): void
    {
        $key = $this->getColumnVisibilityKey();
        session()->put("col_vis.{$key}", $this->visibleColumns);
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
     * 格式：[['key'=>'id','label'=>'ID','sortable'=>true,'exportable'=>true], ...]
     */
    public function getAllColumns(): array
    {
        return [];
    }
}
