<?php

namespace App\Livewire\Traits;

/**
 * 列表页：样板 CRUD Trait
 *
 * 提供列表组件共享的通用 CRUD 方法骨架。
 * 组件只需实现业务差异化方法（save/delete/resetForm/toggleStatus）。
 *
 * 使用方式：
 *   1. 组件 use WithListCrud;
 *   2. 声明 public string $search = '';
 *   3. 如需自定义弹窗属性名，覆盖 getModalPropertyName() / getDeletePropertyName()
 *   4. 实现 resetForm() 重置表单字段
 *   5. 实现 save() / delete() 业务逻辑
 */
trait WithListCrud
{
    use WithToast;

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public string $deleteWarning = '';
    public bool $canDelete = true;

    /**
     * 获取弹窗属性名（组件可覆盖）
     * 默认 'showModal'，组件如用其他属性名则覆盖此方法
     */
    protected function getModalPropertyName(): string
    {
        return 'showModal';
    }

    /**
     * 获取删除确认属性名（组件可覆盖）
     */
    protected function getDeletePropertyName(): string
    {
        return 'showDeleteConfirm';
    }

    /**
     * 打开创建弹窗
     */
    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->{$this->getModalPropertyName()} = true;
    }

    /**
     * 关闭弹窗
     */
    public function closeModal(): void
    {
        $this->{$this->getModalPropertyName()} = false;
        $this->editingId = null;
        $this->resetErrorBag();
        $this->resetForm();
    }

    /**
     * 确认删除弹窗
     */
    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->{$this->getDeletePropertyName()} = true;
    }

    /**
     * 关闭删除确认弹窗
     */
    public function closeDeleteConfirm(): void
    {
        $this->{$this->getDeletePropertyName()} = false;
        $this->deletingId = null;
        $this->deleteWarning = '';
        $this->canDelete = true;
        $this->resetErrorBag();
    }

    /**
     * 重置搜索+翻页+选择
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
        if (method_exists($this, 'clearSelection')) {
            $this->clearSelection();
        }
    }

    /**
     * 组件覆盖：重置表单字段
     */
    public function resetForm(): void
    {
        $this->editingId = null;
    }
}
