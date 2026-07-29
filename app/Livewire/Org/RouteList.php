<?php

namespace App\Livewire\Org;

use App\Models\DeliveryRoute;
use Livewire\Component;
use Livewire\WithPagination;

class RouteList extends Component
{
    use WithPagination;

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
        //
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
            'formStatus' => 'required|in:1,2',
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
            $this->dispatch('toast', message: '配送线路已更新', type: 'success');
        } else {
            DeliveryRoute::create($data);
            $this->dispatch('toast', message: '配送线路已创建', type: 'success');
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
        $this->dispatch('toast', message: '配送线路已删除', type: 'success');
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
        $this->formDescription = '';
        $this->formSort = 0;
        $this->formStatus = 1;
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

        return view('livewire.org.route-list', compact('routes'))
            ->layout('components.app-layout')
            ->title('配送线路管理');
    }
}
