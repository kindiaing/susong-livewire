<?php

namespace App\Livewire\Traits;

/**
 * Toast 消息统一分发 Trait
 *
 * 规范：所有 Livewire 组件通过本 Trait 发送 Toast 通知，
 * 内部统一使用 title + description + type 字段，
 * 与前端 $toast.show({ title, description, type }) 对齐。
 *
 * 使用方式：
 *   1. 在组件中 use WithToast;
 *   2. $this->toastSuccess('操作成功');
 *   3. $this->toastError('操作失败', '详细描述');
 *
 * 禁止直接使用 $this->dispatch('toast', message: '...')，
 * 避免前端 title 为空导致 Toast 无文字的问题。
 */
trait WithToast
{
    protected function toast(string $title, string $description = '', string $type = 'default'): void
    {
        $this->dispatch('toast', title: $title, description: $description, type: $type);
    }

    protected function toastSuccess(string $title, string $description = ''): void
    {
        $this->dispatch('toast', title: $title, description: $description, type: 'success');
    }

    protected function toastError(string $title, string $description = ''): void
    {
        $this->dispatch('toast', title: $title, description: $description, type: 'error');
    }

    protected function toastWarning(string $title, string $description = ''): void
    {
        $this->dispatch('toast', title: $title, description: $description, type: 'warning');
    }

    protected function toastInfo(string $title, string $description = ''): void
    {
        $this->dispatch('toast', title: $title, description: $description, type: 'info');
    }
}
