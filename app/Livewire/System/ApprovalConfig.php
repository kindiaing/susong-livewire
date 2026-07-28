<?php

namespace App\Livewire\System;

use App\Models\ApprovalTypeConfig;
use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalConfig extends Component
{
    use WithPagination;

    public string $filterModule = '';
    public string $filterRisk = '';
    public ?int $editingId = null;
    public string $editDescription = '';

    public function mount(): void
    {
        // 默认无筛选
    }

    /**
     * 切换审核节点启用状态
     */
    public function toggleEnabled(int $id): void
    {
        $config = ApprovalTypeConfig::find($id);
        if (!$config) return;

        $oldEnabled = $config->enabled;
        $config->update(['enabled' => $oldEnabled ? 0 : 1]);

        // 记录审计日志
        AuditLog::log(
            modelType: 'ApprovalTypeConfig',
            modelId: $config->id,
            action: 'update',
            beforeData: ['enabled' => $oldEnabled],
            afterData: ['enabled' => $config->enabled],
            reason: $config->enabled ? '启用审核节点' : '关闭审核节点',
        );

        $status = $config->enabled ? '开启' : '关闭';
        session()->flash('success', "审核节点「{$config->type_name}」已{$status}");
    }

    public function startEdit(int $id): void
    {
        $config = ApprovalTypeConfig::find($id);
        if (!$config) return;

        $this->editingId = $id;
        $this->editDescription = $config->description ?? '';
    }

    public function saveDescription(): void
    {
        $config = ApprovalTypeConfig::find($this->editingId);
        if (!$config) return;

        $config->update(['description' => $this->editDescription]);

        AuditLog::log(
            modelType: 'ApprovalTypeConfig',
            modelId: $config->id,
            action: 'update',
            beforeData: ['description' => $config->description],
            afterData: ['description' => $this->editDescription],
        );

        $this->editingId = null;
        session()->flash('success', "审核节点「{$config->type_name}」说明已更新");
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editDescription = '';
    }

    public function render()
    {
        $query = ApprovalTypeConfig::with(['applicantRole', 'reviewerRole'])->ordered();

        if ($this->filterModule) {
            $query->where('module_name', $this->filterModule);
        }

        if ($this->filterRisk) {
            $query->where('risk_level', $this->filterRisk);
        }

        $configs = $query->get();

        // 获取所有模块列表
        $modules = ApprovalTypeConfig::select('module_name')
            ->whereNotNull('module_name')
            ->distinct()
            ->orderBy('module_name')
            ->pluck('module_name');

        $enabledCount = ApprovalTypeConfig::where('enabled', 1)->count();
        $totalCount = ApprovalTypeConfig::count();

        return view('livewire.system.approval-config', compact('configs', 'modules', 'enabledCount', 'totalCount'))
            ->layout('components.app-layout')
            ->title('审核管理');
    }
}
