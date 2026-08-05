<?php

namespace App\Livewire\System;

use App\Models\SystemConfig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Settings extends Component
{
    public string $activeGroup = 'basic';
    public array $editValues = [];
    public ?int $editingId = null;
    public string $editingValue = '';

    public function mount(): void
    {
        $groups = SystemConfig::groupLabels();
        $hiddenGroups = ['finance', 'money', 'audit'];
        $groups = array_filter($groups, fn($key) => !in_array($key, $hiddenGroups), ARRAY_FILTER_USE_KEY);
        $this->activeGroup = array_key_first($groups);
    }

    public function setActiveGroup(string $group): void
    {
        $this->activeGroup = $group;
        $this->cancelEdit();
    }

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

        // 根据类型预处理值
        $value = $this->editingValue;
        if ($config->config_type === 'boolean') {
            $value = (bool) $value ? '1' : '0';
        }

        // 动态校验
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

        session()->flash('success', "配置项「{$config->label}」已更新");
    }

    public function resetToDefault(int $id): void
    {
        $config = SystemConfig::find($id);
        if (!$config || $config->is_readonly) return;

        $config->update(['config_value' => $config->default_value]);
        SystemConfig::flushCache();

        session()->flash('success', "配置项「{$config->label}」已重置为默认值");
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingValue = '';
    }

    public function render()
    {
        $groups = SystemConfig::groupLabels();

        // 过滤掉已移到财务配置页的分组
        $hiddenGroups = ['finance', 'money', 'audit'];
        $groups = array_filter($groups, fn($key) => !in_array($key, $hiddenGroups), ARRAY_FILTER_USE_KEY);

        $configs = SystemConfig::where('config_group', $this->activeGroup)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.system.settings', compact('groups', 'configs'))
            ->layout('components.app-layout')
            ->title('系统设置');
    }
}
