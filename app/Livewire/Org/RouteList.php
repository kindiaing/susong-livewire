<?php

namespace App\Livewire\Org;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\DeliveryRoute;
use Livewire\Component;
use Livewire\WithPagination;

class RouteList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = DeliveryRoute::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formName = '';
    public string $formDescription = '';
    public int $formSort = 0;
    public int $formStatus = 1;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $route = DeliveryRoute::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $route->name;
        $this->formDescription = $route->description ?? '';
        $this->formSort = $route->sort ?? 0;
        $this->formStatus = $route->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:100',
            'formDescription' => 'nullable|string|max:255',
            'formSort' => 'nullable|integer|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'name' => $validated['formName'],
            'description' => $validated['formDescription'],
            'sort' => $validated['formSort'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $route = DeliveryRoute::findOrFail($this->editingId);
            $route->update($data);
            $this->toastSuccess('配送线路已更新');
        } else {
            DeliveryRoute::create($data);
            $this->toastSuccess('配送线路已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        $route = DeliveryRoute::findOrFail($this->deletingId);
        $route->delete();
        $this->toastSuccess('配送线路已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
        $this->clearSelection();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formDescription = '';
        $this->formSort = 0;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '线路名', 'sortable' => true, 'exportable' => true],
            ['key' => 'code', 'label' => '编码', 'sortable' => false, 'exportable' => true],
            ['key' => 'area', 'label' => '区域', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'code', 'status', 'sort'];
    }

    public function getExportQuery()
    {
        return DeliveryRoute::when($this->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        })->ordered();
    }

    public function getExportFileName(): string
    {
        return '配送线路_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return DeliveryRoute::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '线路名' => 'name',
            '描述' => 'description',
            '排序' => 'sort',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = DeliveryRoute::ordered();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        $routes = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.org.route-list', compact('routes', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('配送线路管理');
    }
}
