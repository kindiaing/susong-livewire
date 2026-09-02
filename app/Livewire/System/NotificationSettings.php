<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithToast;
use App\Models\SystemConfig;
use Livewire\Component;

class NotificationSettings extends Component
{
    use WithToast;

    // ── 左侧导航 ──
    public string $activeGroup = 'switch';

    // ── 通知开关 ──
    public bool $notifySystem = true;
    public bool $notifyOrder = true;
    public bool $notifyRestock = true;
    public bool $notifyInventory = true;
    public bool $notifyAccount = true;

    public function mount(): void
    {
        $this->notifySystem = (bool) setting('notify_system', true);
        $this->notifyOrder = (bool) setting('notify_order', true);
        $this->notifyRestock = (bool) setting('notify_restock', true);
        $this->notifyInventory = (bool) setting('notify_inventory', true);
        $this->notifyAccount = (bool) setting('notify_account', true);
    }

    // ══════════════════════════════════════
    //  导航切换
    // ══════════════════════════════════════

    public function setActiveGroup(string $group): void
    {
        $this->activeGroup = $group;
    }

    // ══════════════════════════════════════
    //  通知开关
    // ══════════════════════════════════════

    public function toggleSwitch(string $key): void
    {
        $map = [
            'notify_system'    => 'notifySystem',
            'notify_order'     => 'notifyOrder',
            'notify_restock'   => 'notifyRestock',
            'notify_inventory' => 'notifyInventory',
            'notify_account'   => 'notifyAccount',
        ];

        if (!isset($map[$key])) return;

        $this->{$map[$key]} = !$this->{$map[$key]};
        SystemConfig::setValue($key, $this->{$map[$key]} ? '1' : '0');

        $labelMap = [
            'notify_system'    => '系统通知',
            'notify_order'     => '订单状态变更',
            'notify_restock'   => '补货提醒',
            'notify_inventory' => '库存预警',
            'notify_account'   => '账户变动',
        ];

        $this->toastSuccess($labelMap[$key] . '已' . ($this->{$map[$key]} ? '开启' : '关闭'));
    }

    // ══════════════════════════════════════
    //  Render
    // ══════════════════════════════════════

    public function render()
    {
        $navGroups = [
            'switch' => '通知开关',
            'list'   => '通知管理',
        ];

        $notificationTypes = [
            ['key' => 'notify_system',    'label' => '系统通知',   'desc' => '管理员手动发送的全站广播和指定用户通知', 'icon' => 'megaphone',     'color' => 'blue',   'prop' => 'notifySystem'],
            ['key' => 'notify_order',     'label' => '订单状态变更', 'desc' => '订单确认、拣货、配送、签收等状态流转通知', 'icon' => 'clipboard-document-list', 'color' => 'green',  'prop' => 'notifyOrder'],
            ['key' => 'notify_restock',   'label' => '补货提醒',    'desc' => '差异处理决策为补货时生成的补货提醒通知',   'icon' => 'shopping-cart', 'color' => 'orange', 'prop' => 'notifyRestock'],
            ['key' => 'notify_inventory', 'label' => '库存预警',    'desc' => 'SKU 库存低于预警阈值时推送的提醒通知',     'icon' => 'exclamation-triangle', 'color' => 'red',    'prop' => 'notifyInventory'],
            ['key' => 'notify_account',   'label' => '账户变动',    'desc' => '充值到账、结算完成、收款确认等资金变动通知', 'icon' => 'banknotes',     'color' => 'purple', 'prop' => 'notifyAccount'],
        ];

        return view('livewire.system.notification-settings', compact('notificationTypes', 'navGroups'))
            ->layout('components.app-layout')
            ->title('通知设置');
    }
}
