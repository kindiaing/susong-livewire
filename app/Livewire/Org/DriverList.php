<?php

namespace App\Livewire\Org;

use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;

class DriverList extends Component
{
    use WithPagination;

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
        //
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
            'formStatus' => 'required|in:1,2',
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
            $this->dispatch('toast', message: '司机已更新', type: 'success');
        } else {
            Driver::create($data);
            $this->dispatch('toast', message: '司机已创建', type: 'success');
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
        $this->dispatch('toast', message: '司机已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
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

        return view('livewire.org.driver-list', compact('drivers'))
            ->layout('components.app-layout')
            ->title('司机管理');
    }
}
