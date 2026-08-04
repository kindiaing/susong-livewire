<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithToast;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AuditSettings extends Component
{
    use WithToast;

    // ── 左侧导航 ──
    public string $activeGroup = 'switch';

    // ── 审计开关 ──
    public bool $auditPurchaseOrder = true;
    public bool $auditPurchaseReturn = true;
    public bool $auditLossOrder = true;
    public bool $auditPriceChange = true;

    // ── 日志策略 ──
    public ?int $editingId = null;
    public string $editingValue = '';

    public function mount(): void
    {
        $this->auditPurchaseOrder = (bool) setting('audit_purchase_order', true);
        $this->auditPurchaseReturn = (bool) setting('audit_purchase_return', true);
        $this->auditLossOrder = (bool) setting('audit_loss_order', true);
        $this->auditPriceChange = (bool) setting('audit_price_change', true);
    }

    // ══════════════════════════════════════
    //  导航切换
    // ══════════════════════════════════════

    public function setActiveGroup(string $group): void
    {
        $this->activeGroup = $group;
        $this->cancelEdit();
    }

    // ══════════════════════════════════════
    //  审计开关
    // ══════════════════════════════════════

    public function toggleAuditSwitch(string $key): void
    {
        $map = [
            'audit_purchase_order'  => 'auditPurchaseOrder',
            'audit_purchase_return' => 'auditPurchaseReturn',
            'audit_loss_order'      => 'auditLossOrder',
            'audit_price_change'    => 'auditPriceChange',
        ];

        if (!isset($map[$key])) return;

        $this->{$map[$key]} = !$this->{$map[$key]};
        SystemConfig::setValue($key, $this->{$map[$key]} ? '1' : '0');

        $labelMap = [
            'audit_purchase_order'  => '采购单状态审计',
            'audit_purchase_return' => '采购退货状态审计',
            'audit_loss_order'      => '损耗单状态审计',
            'audit_price_change'    => '价格变更审计',
        ];

        $this->toastSuccess($labelMap[$key] . '已' . ($this->{$map[$key]} ? '开启' : '关闭'));
    }

    // ══════════════════════════════════════
    //  日志策略编辑
    // ══════════════════════════════════════

    public function startEdit(int $id): void
    {
        $config = SystemConfig::find($id);
        if (!$config || $config->is_readonly) return;

        $this->editingId = $id;
        $this->editingValue = $config->config_value ?? '';
    }

    public function saveEdit(): void
    {
        $config = SystemConfig::find($this->editingId);
        if (!$config || $config->is_readonly) return;

        $value = $this->editingValue;
        if ($config->config_type === 'boolean') {
            $value = (bool) $value ? '1' : '0';
        }

        $rules = [];
        if ($config->validation_rules) {
            $rules['value'] = $config->validation_rules;
        } else {
            $rules['value'] = 'required';
        }

        $validator = Validator::make(['value' => $value], $rules);
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'editingValue' => $validator->errors()->first('value'),
            ]);
        }

        $config->update(['config_value' => $value]);
        SystemConfig::flushCache();

        $this->editingId = null;
        $this->editingValue = '';

        $this->toastSuccess("配置项「{$config->label}」已更新");
    }

    public function resetToDefaultItem(int $id): void
    {
        $config = SystemConfig::find($id);
        if (!$config || $config->is_readonly) return;

        $config->update(['config_value' => $config->default_value]);
        SystemConfig::flushCache();

        $this->toastSuccess("配置项「{$config->label}」已重置为默认值");
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingValue = '';
    }

    // ══════════════════════════════════════
    //  Render
    // ══════════════════════════════════════

    public function render()
    {
        // 审计分组中，排除4个布尔开关（已用独立属性管理），只取非布尔配置
        $auditConfigs = SystemConfig::where('config_group', 'audit')
            ->where('config_type', '!=', 'boolean')
            ->orderBy('sort_order')
            ->get();

        $navGroups = [
            'switch'  => '审计开关',
            'policy'  => '日志策略',
            'logs'    => '审计日志',
        ];

        return view('livewire.system.audit-settings', compact('auditConfigs', 'navGroups'))
            ->layout('components.app-layout')
            ->title('审计设置');
    }
}
