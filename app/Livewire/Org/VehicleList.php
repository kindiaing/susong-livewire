<?php

namespace App\Livewire\Org;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formPlateNumber = '';
    public string $formVehicleType = '';
    public int $formIsColdChain = 0;
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
        $vehicle = Vehicle::findOrFail($id);
        $this->editingId = $id;
        $this->formPlateNumber = $vehicle->plate_number;
        $this->formVehicleType = $vehicle->vehicle_type ?? '';
        $this->formIsColdChain = $vehicle->is_cold_chain;
        $this->formStatus = $vehicle->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'formPlateNumber' => 'required|string|max:20|unique:vehicles,plate_number',
            'formVehicleType' => 'nullable|string|max:50',
            'formIsColdChain' => 'required|in:0,1',
            'formStatus' => 'required|in:1,2',
        ];

        if ($this->editingId) {
            $rules['formPlateNumber'] = 'required|string|max:20|unique:vehicles,plate_number,' . $this->editingId;
        }

        $validated = $this->validate($rules);

        $data = [
            'plate_number' => $validated['formPlateNumber'],
            'vehicle_type' => $validated['formVehicleType'],
            'is_cold_chain' => $validated['formIsColdChain'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $vehicle = Vehicle::findOrFail($this->editingId);
            $vehicle->update($data);
            $this->dispatch('toast', message: '车辆已更新', type: 'success');
        } else {
            Vehicle::create($data);
            $this->dispatch('toast', message: '车辆已创建', type: 'success');
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
        $vehicle = Vehicle::findOrFail($this->deletingId);
        $vehicle->delete();
        $this->dispatch('toast', message: '车辆已删除', type: 'success');
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
        $this->formPlateNumber = '';
        $this->formVehicleType = '';
        $this->formIsColdChain = 0;
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = Vehicle::orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('plate_number', 'like', "%{$this->search}%")
                    ->orWhere('vehicle_type', 'like', "%{$this->search}%");
            });
        }

        $vehicles = $query->paginate(20);

        return view('livewire.org.vehicle-list', compact('vehicles'))
            ->layout('components.app-layout')
            ->title('车辆管理');
    }
}
