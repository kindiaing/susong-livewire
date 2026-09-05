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
    /**
     * 显示时长约定（毫秒）：default/success/info 默认 3000，error/warning 特殊提醒 5000
     * 前端按 type 自动取默认值；$duration 显式传 0 表示不自动关闭，传毫秒数覆盖默认
     */
    protected function toast(string $title, string $description = '', string $type = 'default', ?int $duration = null): void
    {
        $this->dispatch('toast', title: $title, description: $description, type: $type, duration: $duration);
    }

    protected function toastSuccess(string $title, string $description = ''): void
    {
        $this->toast($title, $description, 'success');
    }

    protected function toastError(string $title, string $description = ''): void
    {
        $this->toast($title, $description, 'error');
    }

    protected function toastWarning(string $title, string $description = ''): void
    {
        $this->toast($title, $description, 'warning');
    }

    protected function toastInfo(string $title, string $description = ''): void
    {
        $this->toast($title, $description, 'info');
    }

    /**
     * 长时提醒（5 秒自动关闭）：适用于需要用户留意的重要通知
     */
    protected function toastLong(string $title, string $description = '', string $type = 'info'): void
    {
        $this->toast($title, $description, $type, 5000);
    }
}
