<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithToast;
use App\Models\DeliveryRoute;
use App\Models\DeliveryRouteStop;
use App\Models\Merchant;
use Livewire\Component;

class DeliveryRouteDetail extends Component
{
    use WithToast;

    public DeliveryRoute $route;
    public int $routeId;

    // 商家搜索
    public string $merchantSearch = '';

    // 添加商家弹窗
    public bool $showAddMerchantModal = false;
    public string $addMerchantSearch = '';
    public array $availableMerchants = [];

    // 编辑停靠点弹窗
    public bool $showEditStopModal = false;
    public ?int $editingStopId = null;
    public string $formStopAddress = '';
    public int $formStopServiceTime = 10;
    public string $formStopRemark = '';
    public int $formStopIsActive = 1;

    public function mount(int $id): void
    {
        $this->routeId = $id;
        $this->loadRoute();
    }

    private function loadRoute(): void
    {
        $this->route = DeliveryRoute::with([
            'stops' => fn($q) => $q->orderBy('sequence_no'),
            'stops.merchant',
            'warehouse',
            'defaultDriver',
            'defaultVehicle',
        ])->findOrFail($this->routeId);
    }

    // ========== 拖拽排序 ==========

    public function reorderStops(array $orderedIds): void
    {
        DeliveryRouteStop::batchReorder($this->routeId, $orderedIds);
        DeliveryRouteStop::resequence($this->routeId);
        $this->loadRoute();
        $this->toastSuccess('排序已更新');
    }

    // ========== 添加商家 ==========

    public function openAddMerchantModal(): void
    {
        $this->showAddMerchantModal = true;
        $this->addMerchantSearch = '';
        $this->loadAvailableMerchants();
    }

    public function closeAddMerchantModal(): void
    {
        $this->showAddMerchantModal = false;
        $this->addMerchantSearch = '';
        $this->availableMerchants = [];
    }

    public function updatedAddMerchantSearch(): void
    {
        $this->loadAvailableMerchants();
    }

    private function loadAvailableMerchants(): void
    {
        // 已在该线路中的商家 ID
        $existingIds = $this->route->stops->pluck('merchant_id')->toArray();

        $query = Merchant::whereNotIn('id', $existingIds);

        if ($this->addMerchantSearch) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->addMerchantSearch}%")
                    ->orWhere('contact_phone', 'like', "%{$this->addMerchantSearch}%");
            });
        }

        $this->availableMerchants = $query->enabled()
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'address', 'contact_name', 'contact_phone'])
            ->toArray();
    }

    public function addMerchant(int $merchantId): void
    {
        $merchant = Merchant::findOrFail($merchantId);

        // 获取当前最大 sequence_no
        $maxSeq = DeliveryRouteStop::where('route_id', $this->routeId)->max('sequence_no') ?? 0;

        DeliveryRouteStop::create([
            'route_id' => $this->routeId,
            'merchant_id' => $merchantId,
            'sequence_no' => $maxSeq + 1,
            'address' => $merchant->address,
            'default_service_time' => 10,
            'is_active' => 1,
        ]);

        $this->loadRoute();
        $this->loadAvailableMerchants();
        $this->toastSuccess("已添加商家「{$merchant->name}」");
    }

    // ========== 编辑停靠点 ==========

    public function openEditStopModal(int $stopId): void
    {
        $stop = DeliveryRouteStop::findOrFail($stopId);
        $this->editingStopId = $stopId;
        $this->formStopAddress = $stop->address ?? '';
        $this->formStopServiceTime = $stop->default_service_time;
        $this->formStopRemark = $stop->remark ?? '';
        $this->formStopIsActive = $stop->is_active;
        $this->showEditStopModal = true;
    }

    public function closeEditStopModal(): void
    {
        $this->showEditStopModal = false;
        $this->editingStopId = null;
    }

    public function saveStop(): void
    {
        $validated = $this->validate([
            'formStopAddress' => 'nullable|string|max:255',
            'formStopServiceTime' => 'required|integer|min:1|max:120',
            'formStopRemark' => 'nullable|string|max:500',
            'formStopIsActive' => 'required|in:0,1',
        ]);

        $stop = DeliveryRouteStop::findOrFail($this->editingStopId);
        $stop->update([
            'address' => $validated['formStopAddress'],
            'default_service_time' => $validated['formStopServiceTime'],
            'remark' => $validated['formStopRemark'],
            'is_active' => $validated['formStopIsActive'],
        ]);

        $this->showEditStopModal = false;
        $this->editingStopId = null;
        $this->loadRoute();
        $this->toastSuccess('停靠点已更新');
    }

    // ========== 移除商家 ==========

    public function removeStop(int $stopId): void
    {
        $stop = DeliveryRouteStop::findOrFail($stopId);
        $stop->delete();
        DeliveryRouteStop::resequence($this->routeId);
        $this->loadRoute();
        $this->toastSuccess('已移除商家');
    }

    // ========== 切换启用/停用 ==========

    public function toggleStopActive(int $stopId): void
    {
        $stop = DeliveryRouteStop::findOrFail($stopId);
        $stop->update(['is_active' => $stop->is_active ? 0 : 1]);
        $this->loadRoute();
        $this->toastSuccess($stop->is_active ? '已启用' : '已停用');
    }

    public function render()
    {
        $route = $this->route;
        $stops = $route->stops;

        return view('livewire.delivery.delivery-route-detail', compact('route', 'stops'))
            ->layout('components.app-layout')
            ->title("配送线路 - {$route->name}");
    }
}
