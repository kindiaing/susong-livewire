<?php

namespace App\Livewire\Traits;

/**
 * 通用操作二次确认 Trait
 *
 * 用于"危险操作"（作废/关闭等不可逆状态变更）与"审核类操作"的二次确认弹窗。
 * 与 WithListCrud 的删除确认（confirmDelete/delete）互不干扰。
 *
 * 使用方式：
 *   1. 组件 use WithConfirmAction;
 *   2. 视图按钮 wire:click="confirmAction('目标方法', '弹窗标题', '补充说明', 参数...)"
 *   3. 视图底部 @include('partials.action-confirm')
 *   4. 用户确认后 executeActionConfirm 调用目标方法并关闭弹窗
 */
trait WithConfirmAction
{
    use WithToast;

    public bool $showActionConfirm = false;
    public string $pendingAction = '';
    public string $actionConfirmTitle = '';
    public string $actionConfirmText = '';

    /** @var array<int, mixed> 目标方法的位置参数 */
    public array $pendingActionParams = [];

    /**
     * 打开操作确认弹窗（供 wire:click 直接调用）
     *
     * wire:click="confirmAction('cancelOrder', '确认作废？', '此操作不可撤销', {{ $item->id }})"
     */
    public function confirmAction(string $method, string $title = '确认执行此操作？', string $text = '', mixed ...$params): void
    {
        $this->pendingAction = $method;
        $this->actionConfirmTitle = $title;
        $this->actionConfirmText = $text;
        $this->pendingActionParams = array_values($params);
        $this->showActionConfirm = true;
    }

    public function closeActionConfirm(): void
    {
        $this->showActionConfirm = false;
        $this->pendingAction = '';
        $this->pendingActionParams = [];
        $this->actionConfirmTitle = '';
        $this->actionConfirmText = '';
        $this->resetErrorBag();
    }

    public function executeActionConfirm(): void
    {
        $method = $this->pendingAction;
        $params = $this->pendingActionParams;
        $this->closeActionConfirm();

        if ($method === '' || ! method_exists($this, $method)) {
            $this->toastError('无效的操作');

            return;
        }

        $this->{$method}(...$params);
    }
}
