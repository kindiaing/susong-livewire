<?php

namespace App\Livewire\Price;

use App\Livewire\Traits\WithToast;
use App\Models\SystemConfig;
use App\Services\PricingService;
use Livewire\Component;

class PricingConfig extends Component
{
    use WithToast;

    public string $pricingMode = 'lowest';
    public array $pricingPriority = [];
    public array $sourceEnabled = [];
    public array $sourceLabels = [];
    public array $prioritySortNumbers = [];
    public bool $showResetConfirm = false;

    // 模式切换确认弹窗
    public bool $showModeSwitchConfirm = false;
    public string $pendingMode = '';

    public function mount(): void
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

    /**
     * 请求切换模式（弹窗确认）
     */
    public function requestModeSwitch(string $mode): void
    {
        if ($mode === $this->pricingMode) return;
        $this->pendingMode = $mode;
        $this->showModeSwitchConfirm = true;
    }

    /**
     * 确认切换模式
     */
    public function confirmModeSwitch(): void
    {
        $this->pricingMode = $this->pendingMode;
        SystemConfig::setValue('pricing_mode', $this->pricingMode);
        $this->showModeSwitchConfirm = false;
        $this->pendingMode = '';
        $this->toastSuccess('取价模式已更新');
    }

    /**
     * 取消切换模式
     */
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

    /**
     * 保存优先级排序号
     */
    public function savePrioritySort(): void
    {
        $paired = [];
        foreach ($this->pricingPriority as $source) {
            $sortNum = (int) ($this->prioritySortNumbers[$source] ?? 0);
            if ($sortNum < 1) $sortNum = 99;
            $paired[] = ['source' => $source, 'sort' => $sortNum];
        }

        // 按排序号从小到大排列
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

    public function render()
    {
        return view('livewire.price.pricing-config')
            ->layout('components.app-layout')
            ->title('取价配置');
    }
}
