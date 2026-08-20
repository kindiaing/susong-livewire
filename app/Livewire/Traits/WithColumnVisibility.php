<?php

namespace App\Livewire\Traits;

use App\Models\UserPreference;

/**
 * 列表页：自定义列显示 Trait
 * - 支持显示/隐藏列
 * - 双模式持久化：
 *   - 默认：localStorage（前端 Alpine.js 实现，零 HTTP 请求）
 *   - DB 模式：UserPreference 表（组件重写 useDbColumnVisibility() 返回 true）
 * - 无持久化数据时回退到 getDefaultColumns() 代码默认值
 */
trait WithColumnVisibility
{
    public bool $showColumnModal = false;

    public array $visibleColumns = [];

    /**
     * 是否使用 DB 持久化（组件可重写为 true）
     * 默认 false = localStorage 模式
     */
    public function useDbColumnVisibility(): bool
    {
        return false;
    }

    /**
     * 初始化列可见性（组件 mount 中调用）
     * - DB 模式：从 UserPreference 表读取
     * - localStorage 模式：设为代码默认值，前端 Alpine 加载后从 localStorage 覆盖
     */
    public function initColumnVisibility(): void
    {
        if ($this->useDbColumnVisibility() && auth()->check()) {
            $saved = UserPreference::getPreference(auth()->id(), $this->getColumnVisibilityKey());
            $this->visibleColumns = (is_array($saved) && !empty($saved))
                ? $saved
                : $this->getDefaultColumns();
        } else {
            $this->visibleColumns = $this->getDefaultColumns();
        }
    }

    public function openColumnModal(): void
    {
        $this->showColumnModal = true;
    }

    /**
     * 切换列显隐
     * - DB 模式：即时写入 DB
     * - localStorage 模式：dispatch 事件由前端 Alpine 写 localStorage
     */
    public function toggleColumn(string $column): void
    {
        $idx = array_search($column, $this->visibleColumns);
        if ($idx !== false) {
            unset($this->visibleColumns[$idx]);
            $this->visibleColumns = array_values($this->visibleColumns);
        } else {
            $this->visibleColumns[] = $column;
        }

        if ($this->useDbColumnVisibility() && auth()->check()) {
            UserPreference::setPreference(auth()->id(), $this->getColumnVisibilityKey(), $this->visibleColumns);
        } else {
            $this->dispatch('save-column-visibility', key: $this->getColumnVisibilityKey(), columns: $this->visibleColumns);
        }
    }

    /**
     * 恢复默认列
     */
    public function resetColumns(): void
    {
        $this->visibleColumns = $this->getDefaultColumns();

        if ($this->useDbColumnVisibility() && auth()->check()) {
            UserPreference::setPreference(auth()->id(), $this->getColumnVisibilityKey(), $this->visibleColumns);
        } else {
            $this->dispatch('save-column-visibility', key: $this->getColumnVisibilityKey(), columns: $this->visibleColumns);
        }
    }

    /**
     * 全选所有列
     */
    public function selectAllColumns(): void
    {
        $this->visibleColumns = collect($this->getAllColumns())->pluck('key')->toArray();

        if ($this->useDbColumnVisibility() && auth()->check()) {
            UserPreference::setPreference(auth()->id(), $this->getColumnVisibilityKey(), $this->visibleColumns);
        } else {
            $this->dispatch('save-column-visibility', key: $this->getColumnVisibilityKey(), columns: $this->visibleColumns);
        }
    }

    public function closeColumnModal(): void
    {
        $this->showColumnModal = false;
    }

    public function isColumnVisible(string $column): bool
    {
        return in_array($column, $this->visibleColumns);
    }

    /**
     * 组件覆盖：唯一标识（默认用类名）
     * 用于 localStorage key / DB pref_key 区分不同列表页
     */
    public function getColumnVisibilityKey(): string
    {
        return str_replace('\\', '_', static::class);
    }

    /**
     * 组件覆盖：默认显示的列 key 数组
     * 默认行为：排除 id 列后全部显示（兼容未定义的组件）
     * 组件应覆盖此方法，只返回 5-8 个关键业务列
     */
    public function getDefaultColumns(): array
    {
        return collect($this->getAllColumns())
            ->pluck('key')
            ->reject(fn($key) => $key === 'id')
            ->values()
            ->toArray();
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
