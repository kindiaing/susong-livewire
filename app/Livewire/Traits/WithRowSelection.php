<?php

namespace App\Livewire\Traits;

/**
 * 列表页：行选择 Trait
 * - 支持 checkbox 单行/全页选择
 * - 提供批量操作基础方法
 */
trait WithRowSelection
{
    use WithToast;

    public array $selectedIds = [];
    public bool $selectAllPage = false;

    /**
     * 全页选择/取消
     * 组件需要实现 getPageIds(): array 返回当前页所有 ID
     */
    public function updatedSelectAllPage(bool $value): void
    {
        if ($value) {
            $this->selectedIds = $this->getPageIds();
        } else {
            $this->selectedIds = [];
        }
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAllPage = false;
    }

    /**
     * 单行选择切换
     */
    public function toggleSelect($id): void
    {
        $id = (int) $id;
        $key = array_search($id, $this->selectedIds);
        if ($key !== false) {
            unset($this->selectedIds[$key]);
            $this->selectedIds = array_values($this->selectedIds);
        } else {
            $this->selectedIds[] = $id;
        }
        // 同步全选状态
        $pageIds = $this->getPageIds();
        $this->selectAllPage = !empty($pageIds) && empty(array_diff($pageIds, $this->selectedIds));
    }

    public function getSelectedIds(): array
    {
        return $this->selectedIds;
    }

    public function getSelectedCount(): int
    {
        return count($this->selectedIds);
    }

    /**
     * 组件可覆盖此方法返回当前页数据 ID
     */
    public function getPageIds(): array
    {
        return [];
    }

    /**
     * 批量删除 - 组件可覆盖
     */
    public function batchDelete(): void
    {
        if (empty($this->selectedIds)) {
            $this->toastError('请先选择数据');
            return;
        }
        $modelClass = property_exists($this, 'modelClass') ? $this->modelClass : null;
        if ($modelClass) {
            $modelClass::destroy($this->selectedIds);
            $count = count($this->selectedIds);
            $this->clearSelection();
            $this->toastSuccess("已删除 {$count} 条数据");
        }
    }

    /**
     * 批量修改状态 - 组件可覆盖
     */
    public function batchUpdateStatus(int $status): void
    {
        if (empty($this->selectedIds)) {
            $this->toastError('请先选择数据');
            return;
        }
        $modelClass = property_exists($this, 'modelClass') ? $this->modelClass : null;
        if ($modelClass) {
            $modelClass::whereIn('id', $this->selectedIds)->update(['status' => $status]);
            $this->clearSelection();
            $label = $status === 1 ? '启用' : '禁用';
            $this->toastSuccess("已{$label}所选数据");
        }
    }
}
