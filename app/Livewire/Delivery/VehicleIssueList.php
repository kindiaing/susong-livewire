<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\VehicleIssue;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleIssueList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = VehicleIssue::class;

    public string $search = '';
    public int $filterStatus = 0;
    public int $filterVehicleId = 0;

    // 新增/编辑表单
    public int $formVehicleId = 0;
    public string $formIssueType = 'breakdown';
    public string $formDescription = '';
    public string $formImpactType = '';
    public string $formImpactDesc = '';

    public static array $statusMap = [
        1 => '处理中', 2 => '已解决', 3 => '已关闭',
    ];

    public static array $statusColorMap = [
        1 => 'orange', 2 => 'green', 3 => 'gray',
    ];

    public static array $issueTypeMap = [
        'breakdown' => '抛锚', 'accident' => '事故', 'tire' => '轮胎',
        'battery' => '电瓶', 'engine' => '发动机', 'other' => '其他',
    ];

    public static array $impactTypeMap = [
        'delay' => '配送延迟', 'reroute' => '改道', 'cancel' => '任务取消', 'none' => '无影响',
    ];

    public array $vehicleOptions = [];

    public function mount(): void
    {
        $this->initColumnVisibility();
        $this->vehicleOptions = Vehicle::orderBy('plate_number')
            ->get(['id', 'plate_number'])
            ->mapWithKeys(fn($v) => [$v->id => $v->plate_number])
            ->toArray();
    }

    // ========== 弹窗操作 ==========

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $issue = VehicleIssue::findOrFail($id);
        $this->editingId = $id;
        $this->formVehicleId = $issue->vehicle_id;
        $this->formIssueType = $issue->issue_type ?? 'other';
        $this->formDescription = $issue->description ?? '';
        $this->formImpactType = $issue->impact_type ?? '';
        $this->formImpactDesc = $issue->impact_desc ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formVehicleId' => 'required|integer|min:1',
            'formIssueType' => 'required|string',
            'formDescription' => 'required|string|max:500',
            'formImpactType' => 'nullable|string',
            'formImpactDesc' => 'nullable|string|max:500',
        ]);

        $data = [
            'vehicle_id' => $validated['formVehicleId'],
            'issue_type' => $validated['formIssueType'],
            'description' => $validated['formDescription'],
            'impact_type' => $validated['formImpactType'] ?: null,
            'impact_desc' => $validated['formImpactDesc'] ?: null,
        ];

        if ($this->editingId) {
            VehicleIssue::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('故障记录已更新');
        } else {
            $data['reported_at'] = now();
            $data['status'] = VehicleIssue::STATUS_OPEN;
            VehicleIssue::create($data);
            $this->toastSuccess('故障记录已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    // ========== 状态操作 ==========

    public function resolveIssue(int $id): void
    {
        $issue = VehicleIssue::findOrFail($id);
        if ($issue->status !== VehicleIssue::STATUS_OPEN) {
            $this->toastError('仅处理中记录可标记解决');
            return;
        }
        $issue->update([
            'status' => VehicleIssue::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
        $this->toastSuccess('故障已解决');
    }

    public function closeIssue(int $id): void
    {
        $issue = VehicleIssue::findOrFail($id);
        if (!in_array($issue->status, [VehicleIssue::STATUS_OPEN, VehicleIssue::STATUS_RESOLVED])) {
            $this->toastError('当前状态不允许关闭');
            return;
        }
        $issue->update(['status' => VehicleIssue::STATUS_CLOSED]);
        $this->toastSuccess('记录已关闭');
    }

    public function delete(): void
    {
        VehicleIssue::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('故障记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 0;
        $this->filterVehicleId = 0;
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formVehicleId = 0;
        $this->formIssueType = 'breakdown';
        $this->formDescription = '';
        $this->formImpactType = '';
        $this->formImpactDesc = '';
    }

    // ========== 列配置 ==========

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'vehicle', 'label' => '车辆', 'sortable' => false, 'exportable' => true],
            ['key' => 'issue_type', 'label' => '故障类型', 'sortable' => false, 'exportable' => true],
            ['key' => 'description', 'label' => '描述', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'impact_type', 'label' => '影响类型', 'sortable' => false, 'exportable' => true],
            ['key' => 'reported_at', 'label' => '上报时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'resolved_at', 'label' => '解决时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['vehicle', 'issue_type', 'description', 'status', 'reported_at'];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(VehicleIssue::with(['vehicle']))->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '车辆故障_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->applyFilters(VehicleIssue::query())
            ->forPage($this->getPage(), setting('per_page', 10))
            ->pluck('id')
            ->toArray();
    }

    private function applyFilters($query)
    {
        return $query
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('description', 'like', "%{$this->search}%")
                        ->orWhere('impact_desc', 'like', "%{$this->search}%")
                        ->orWhereHas('vehicle', function ($vq) {
                            $vq->where('plate_number', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterStatus >= 1, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterVehicleId > 0, function ($q) {
                $q->where('vehicle_id', $this->filterVehicleId);
            });
    }

    private function items()
    {
        return $this->applyFilters(VehicleIssue::with(['vehicle']))
            ->orderBy('id', 'desc')
            ->paginate(setting('per_page', 10));
    }

    public function render()
    {
        $items = $this->items();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.delivery.vehicle-issue-list', compact(
            'items', 'allColumns', 'selectedCount'
        ))
            ->layout('components.app-layout')
            ->title('车辆故障');
    }
}
