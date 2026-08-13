<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class UnitList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Unit::class;

    public string $search = '';

    // 表单字段
    public string $name = '';
    public string $symbol = '';
    public int $formStatus = 1;
    public int $sort = 0;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'symbol', 'sort', 'status', 'created_at'];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '单位名称', 'sortable' => false, 'exportable' => true],
            ['key' => 'symbol', 'label' => '简称', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Unit::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('symbol', 'like', "%{$this->search}%");
        })->orderBy('sort')->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '单位管理_' . now()->format('Ymd_His');
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'symbol' => $row->symbol ?? '',
                'sort' => $row->sort,
                'status' => Unit::statusMap()[$row->status] ?? '未知',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->symbol = '';
        $this->formStatus = 1;
        $this->sort = 0;
    }

    public function openEditModal(int $id): void
    {
        $unit = Unit::findOrFail($id);
        $this->editingId = $id;
        $this->name = $unit->name;
        $this->symbol = $unit->symbol ?? '';
        $this->formStatus = $unit->status;
        $this->sort = $unit->sort;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:20',
            'symbol' => 'nullable|string|max:10',
            'sort' => 'integer|min:0',
        ], [
            'name.required' => '请输入单位名称',
            'name.max' => '单位名称最多20个字符',
            'symbol.max' => '简称最多10个字符',
        ]);

        $data = [
            'name' => $this->name,
            'symbol' => $this->symbol ?: null,
            'status' => $this->formStatus,
            'sort' => $this->sort,
        ];

        if ($this->editingId) {
            $unit = Unit::findOrFail($this->editingId);
            $unit->update($data);
            $this->toastSuccess('更新成功');
        } else {
            Unit::create($data);
            $this->toastSuccess('创建成功');
        }

        $this->closeModal();
    }

    public function delete(): void
    {
        $unit = Unit::findOrFail($this->deletingId);

        // 检查是否被换算关系引用
        $usedCount = \App\Models\UnitConversion::where('from_unit_id', $unit->id)
            ->orWhere('to_unit_id', $unit->id)
            ->count();
        if ($usedCount > 0) {
            $this->toastError("该单位已被 {$usedCount} 条换算关系引用，无法删除");
            $this->showDeleteConfirm = false;
            $this->deletingId = null;
            return;
        }

        $unit->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function toggleStatus(int $id): void
    {
        $unit = Unit::findOrFail($id);
        $unit->status = $unit->status === Unit::STATUS_ENABLED ? Unit::STATUS_DISABLED : Unit::STATUS_ENABLED;
        $unit->save();
        $this->toastSuccess($unit->status ? '已启用' : '已禁用');
    }

    public function render()
    {
        $query = Unit::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('symbol', 'like', "%{$this->search}%");
        })->orderBy('sort')->orderBy('id', 'desc');

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.system.unit-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('单位管理');
    }
}
