<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithToast;
use App\Models\SystemConfig;
use App\Services\PricingService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FinanceSettings extends Component
{
    use WithToast;

    // ── Tab 切换 ──
    public string $activeTab = 'pricing';

    // ── 取价配置 ──
    public string $pricingMode = 'lowest';
    public array $pricingPriority = [];
    public array $sourceEnabled = [];
    public array $sourceLabels = [];
    public array $prioritySortNumbers = [];
    public bool $showResetConfirm = false;
    public bool $showModeSwitchConfirm = false;
    public string $pendingMode = '';

    // ── 通用配置编辑（财务风控 / 金额精度） ──
    public ?int $editingId = null;
    public string $editingValue = '';

    public function mount(): void
    {
        $this->initPricingConfig();
    }

    // ══════════════════════════════════════
    //  Tab 切换
    // ══════════════════════════════════════

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->cancelEdit();
    }

    // ══════════════════════════════════════
    //  取价配置
    // ══════════════════════════════════════

    protected function initPricingConfig(): void
    {
        $this->sourceLabels = PricingService::sourceLabelMap();

        $this->pricingMode = setting('pricing_mode', 'lowest');

        $priority = setting('pricing_priority', ['promotion', 'store', 'member', 'channel', 'retail']);
        if (is_string($priority)) {
            $priority = json_decode($priority, true) ?? ['promotion', 'store', 'member', 'channel', 'retail'];
        }
        $this->pricingPriority = $priority;

        $sourceEnabled = setting('pricing_source_enabled', [
            'promotion' => true, 'store' => true, 'member' => true, 'channel' => true, 'retail' => true,
        ]);
        if (is_string($sourceEnabled)) {
            $sourceEnabled = json_decode($sourceEnabled, true) ?? [];
        }
        $this->sourceEnabled = $sourceEnabled;

        $this->syncPrioritySortNumbers();
    }

    public function requestModeSwitch(string $mode): void
    {
        if ($mode === $this->pricingMode) return;
        $this->pendingMode = $mode;
        $this->showModeSwitchConfirm = true;
    }

    public function confirmModeSwitch(): void
    {
        $this->pricingMode = $this->pendingMode;
        SystemConfig::setValue('pricing_mode', $this->pricingMode);
        $this->showModeSwitchConfirm = false;
        $this->pendingMode = '';
        $this->toastSuccess('取价模式已更新');
    }

    public function cancelModeSwitch(): void
    {
        $this->showModeSwitchConfirm = false;
        $this->pendingMode = '';
    }

    public function toggleSource(string $source): void
    {
        if ($source === 'retail') {
            $this->toastWarning('标准零售价不可关闭');
            return;
        }

        $this->sourceEnabled[$source] = !($this->sourceEnabled[$source] ?? true);
        SystemConfig::setValue('pricing_source_enabled', json_encode($this->sourceEnabled));
        $this->toastSuccess('来源开关已更新');
    }

    public function savePrioritySort(): void
    {
        $paired = [];
        foreach ($this->pricingPriority as $source) {
            $sortNum = (int) ($this->prioritySortNumbers[$source] ?? 0);
            if ($sortNum < 1) $sortNum = 99;
            $paired[] = ['source' => $source, 'sort' => $sortNum];
        }

        usort($paired, fn($a, $b) => $a['sort'] <=> $b['sort']);

        $this->pricingPriority = array_map(fn($item) => $item['source'], $paired);
        $this->syncPrioritySortNumbers();

        SystemConfig::setValue('pricing_priority', json_encode($this->pricingPriority));
        $this->toastSuccess('优先级顺序已更新');
    }

    public function openResetConfirm(): void
    {
        $this->showResetConfirm = true;
    }

    public function closeResetConfirm(): void
    {
        $this->showResetConfirm = false;
    }

    public function resetToDefault(): void
    {
        SystemConfig::resetToDefault('pricing_mode');
        SystemConfig::resetToDefault('pricing_priority');
        SystemConfig::resetToDefault('pricing_source_enabled');

        $this->pricingMode = 'lowest';
        $this->pricingPriority = ['promotion', 'store', 'member', 'channel', 'retail'];
        $this->sourceEnabled = ['promotion' => true, 'store' => true, 'member' => true, 'channel' => true, 'retail' => true];
        $this->syncPrioritySortNumbers();
        $this->showResetConfirm = false;

        $this->toastSuccess('已重置为默认配置');
    }

    protected function syncPrioritySortNumbers(): void
    {
        $this->prioritySortNumbers = [];
        foreach ($this->pricingPriority as $index => $source) {
            $this->prioritySortNumbers[$source] = $index + 1;
        }
    }

    // ══════════════════════════════════════
    //  通用配置编辑（财务风控 / 金额精度）
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
        $financeConfigs = SystemConfig::where('config_group', 'finance')
            ->whereNotIn('config_key', ['pricing_mode', 'pricing_source_enabled', 'pricing_priority'])
            ->orderBy('sort_order')
            ->get();

        $moneyConfigs = SystemConfig::where('config_group', 'money')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.system.finance-settings', compact('financeConfigs', 'moneyConfigs'))
            ->layout('components.app-layout')
            ->title('财务配置');
    }
}
