<?php

namespace App\Livewire\Org;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;

class DriverList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = Driver::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formName = '';
    public string $formPhone = '';
    public string $formIdCard = '';
    public int $formOnlineStatus = 0;
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
        $driver = Driver::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $driver->name;
        $this->formPhone = $driver->phone;
        $this->formIdCard = $driver->id_card ?? '';
        $this->formOnlineStatus = $driver->online_status;
        $this->formStatus = $driver->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'formName' => 'required|string|max:50',
            'formPhone' => 'required|string|max:20|unique:drivers,phone',
            'formIdCard' => 'nullable|string|max:20',
            'formOnlineStatus' => 'required|in:0,1',
            'formStatus' => 'required|in:0,1',
        ];

        if ($this->editingId) {
            $rules['formPhone'] = 'required|string|max:20|unique:drivers,phone,' . $this->editingId;
        }

        $validated = $this->validate($rules);

        $data = [
            'name' => $validated['formName'],
            'phone' => $validated['formPhone'],
            'id_card' => $validated['formIdCard'],
            'online_status' => $validated['formOnlineStatus'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $driver = Driver::findOrFail($this->editingId);
            $driver->update($data);
            $this->toastSuccess('司机已更新');
        } else {
            Driver::create($data);
            $this->toastSuccess('司机已创建');
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
        $driver = Driver::findOrFail($this->deletingId);
        $driver->delete();
        $this->toastSuccess('司机已删除');
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
        $this->formPhone = '';
        $this->formIdCard = '';
        $this->formOnlineStatus = 0;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true, 'width' => '60px'],
            ['key' => 'name', 'label' => '姓名', 'sortable' => true, 'exportable' => true, 'width' => '1fr'],
            ['key' => 'phone', 'label' => '手机号', 'sortable' => false, 'exportable' => true, 'width' => '120px'],
            ['key' => 'id_number', 'label' => '身份证号', 'sortable' => false, 'exportable' => true, 'width' => '160px'],
            ['key' => 'online_status', 'label' => '在线状态', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true, 'width' => '180px'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true, 'width' => '150px'],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'phone', 'status', 'created_at'];
    }

    public function getExportQuery()
    {
        return Driver::when($this->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('id_card', 'like', "%{$this->search}%");
            });
        })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '司机_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Driver::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '姓名' => 'name',
            '手机号' => 'phone',
            '身份证号' => 'id_card',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Driver::orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('id_card', 'like', "%{$this->search}%");
            });
        }

        $drivers = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.org.driver-list', compact('drivers', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('司机管理');
    }
}
