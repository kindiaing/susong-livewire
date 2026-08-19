<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\AuditLog;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sku;
use App\Services\UnitConversionService;
use Livewire\Component;
use Livewire\WithPagination;

class CartList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = CartItem::class;

    public string $search = '';

    // 筛选
    public ?int $filterMerchantId = null;

    // 表单
    public int $formMerchantId = 0;
    public int $formSkuId = 0;
    public int $formQuantity = 0;
    public ?int $formUnitId = null;
    public int $formUnitQuantity = 0;
    public string $formUnitPrice = '';

    // 生成订单确认弹窗
    public bool $showCreateOrderConfirm = false;
    public ?int $createOrderMerchantId = null;
    public string $createOrderMerchantName = '';
    public int $createOrderItemCount = 0;
    public int $createOrderTotalAmount = 0;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'product_name', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'quantity', 'label' => '数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'price', 'label' => '单价', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'subtotal', 'label' => '金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['merchant_id', 'sku_id', 'product_name', 'quantity', 'price', 'subtotal'];
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '购物车_' . now()->format('Ymd_His');
    }

    public function getExportRowCallback(): callable
    {
        return function (CartItem $row) {
            return [
                'id' => $row->id,
                'merchant_id' => $row->cart?->merchant?->name ?? '',
                'sku_id' => $row->sku?->sku_code ?? '',
                'product_name' => $row->sku?->product?->name ?? '',
                'quantity' => $row->quantity,
                'price' => money_format($row->price, false),
                'subtotal' => money_format($row->quantity * $row->price, false),
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportModelClass(): string
    {
        return CartItem::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '购物车ID' => 'cart_id',
            'SKU ID' => 'sku_id',
            '数量' => 'quantity',
            '单价(厘)' => 'price',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['cart_id', 'sku_id'];
    }

    public function getImportMoneyFields(): array
    {
        return ['price'];
    }

    public function getPageIds(): array
    {
        return $this->buildQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * SKU 切换时加载可用单位列表
     */
    public function updatedFormSkuId(int $value): void
    {
        $this->formUnitId = null;
        $this->formUnitQuantity = 0;
        $this->formQuantity = 0;
        if ($value > 0) {
            $this->selectSkuUnit();
        }
    }

    /**
     * 单位或单位数量变更时自动换算为 base_unit 数量
     */
    public function updatedFormUnitId(): void
    {
        $this->recalculateQuantity();
    }

    public function updatedFormUnitQuantity(): void
    {
        $this->recalculateQuantity();
    }

    /**
     * 根据 SKU 的 base_unit 设置默认单位
     */
    public function selectSkuUnit(): void
    {
        if ($this->formSkuId <= 0) {
            return;
        }
        $sku = Sku::find($this->formSkuId);
        if ($sku && $sku->base_unit_id) {
            $this->formUnitId = $sku->base_unit_id;
            $this->recalculateQuantity();
        }
    }

    /**
     * 根据选中的单位 + 单位数量，自动换算为 base_unit 的 formQuantity
     */
    private function recalculateQuantity(): void
    {
        if ($this->formSkuId > 0 && $this->formUnitId && $this->formUnitQuantity > 0) {
            $svc = app(UnitConversionService::class);
            $this->formQuantity = $svc->convertToBase($this->formSkuId, $this->formUnitId, $this->formUnitQuantity);
        } elseif ($this->formSkuId > 0 && !$this->formUnitId && $this->formUnitQuantity > 0) {
            $this->formQuantity = $this->formUnitQuantity;
        } elseif ($this->formSkuId > 0 && $this->formUnitQuantity === 0) {
            $this->formQuantity = 0;
        }
    }

    /**
     * 获取当前 SKU 可用单位列表（用于下拉）
     */
    #[\Livewire\Attributes\Computed]
    public function availableUnits(): array
    {
        if ($this->formSkuId <= 0) {
            return [];
        }
        $svc = app(UnitConversionService::class);
        return $svc->getAvailableUnits($this->formSkuId);
    }

    /**
     * 获取换算预览文本
     */
    #[\Livewire\Attributes\Computed]
    public function unitPreview(): string
    {
        if ($this->formSkuId <= 0 || !$this->formUnitId || $this->formUnitQuantity <= 0) {
            return '';
        }
        $svc = app(UnitConversionService::class);
        return $svc->formatWithConversion($this->formSkuId, $this->formUnitId, $this->formUnitQuantity);
    }

    public function openEditModal(int $id): void
    {
        $item = CartItem::findOrFail($id);
        $this->editingId = $id;
        $this->formMerchantId = $item->cart?->merchant_id ?? 0;
        $this->formSkuId = $item->sku_id;
        $this->formQuantity = $item->quantity;
        $this->formUnitPrice = $this->centsToYuan($item->price);
        // 回填单位信息
        if ($item->unit_id && $item->unit_quantity) {
            $this->formUnitId = $item->unit_id;
            $this->formUnitQuantity = $item->unit_quantity;
        } else {
            $sku = Sku::find($item->sku_id);
            $this->formUnitId = $sku?->base_unit_id;
            $this->formUnitQuantity = $item->quantity;
        }
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $validated = $this->validate([
                'formQuantity' => 'required|integer|min:1',
            ]);
            CartItem::findOrFail($this->editingId)->update([
                'quantity' => $validated['formQuantity'],
                'unit_id' => $this->formUnitId,
                'unit_quantity' => $this->formUnitQuantity ?: null,
            ]);
            $this->toastSuccess('购物车已更新');
        } else {
            $validated = $this->validate([
                'formMerchantId' => 'required|integer|exists:merchants,id',
                'formSkuId' => 'required|integer|exists:skus,id',
                'formQuantity' => 'required|integer|min:1',
                'formUnitPrice' => 'required|numeric|min:0',
            ]);

            // 获取或创建商家的购物车
            $cart = Cart::firstOrCreate(
                ['merchant_id' => $validated['formMerchantId']],
                ['merchant_id' => $validated['formMerchantId']]
            );

            // 同 SKU 累加数量
            $existing = CartItem::where('cart_id', $cart->id)
                ->where('sku_id', $validated['formSkuId'])
                ->first();

            if ($existing) {
                $existing->increment('quantity', $validated['formQuantity']);
                $existing->update([
                    'unit_id' => $this->formUnitId,
                    'unit_quantity' => ($existing->unit_quantity ?? 0) + $this->formUnitQuantity,
                ]);
                $this->toastSuccess('购物车数量已累加');
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'sku_id' => $validated['formSkuId'],
                    'quantity' => $validated['formQuantity'],
                    'price' => money_to_cents($validated['formUnitPrice']),
                    'unit_id' => $this->formUnitId,
                    'unit_quantity' => $this->formUnitQuantity ?: null,
                ]);
                $this->toastSuccess('购物车已创建');
            }
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        CartItem::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('购物车项已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function batchDelete(): void
    {
        $count = count($this->selectedIds);
        if ($count === 0) {
            $this->toastWarning('请先选择要删除的记录');
            return;
        }
        CartItem::whereIn('id', $this->selectedIds)->delete();
        $this->toastSuccess("已删除 {$count} 条记录");
        $this->clearSelection();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterMerchantId = null;
        $this->resetPage();
        $this->clearSelection();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formSkuId = 0;
        $this->formQuantity = 0;
        $this->formUnitId = null;
        $this->formUnitQuantity = 0;
        $this->formUnitPrice = '';
    }

    // ── 生成订单 ──

    /**
     * 弹出确认弹窗：将指定商户的购物车明细生成订单
     */
    public function confirmCreateOrder(int $merchantId): void
    {
        $merchant = Merchant::find($merchantId);
        if (!$merchant) {
            $this->toastError('商家不存在');
            return;
        }

        $cart = Cart::where('merchant_id', $merchantId)->first();
        if (!$cart) {
            $this->toastError('该商家无购物车数据');
            return;
        }

        $items = CartItem::where('cart_id', $cart->id)->with('sku.product')->get();
        if ($items->isEmpty()) {
            $this->toastError('该商家购物车为空');
            return;
        }

        $totalAmount = $items->sum(fn($item) => $item->quantity * $item->price);

        $this->createOrderMerchantId = $merchantId;
        $this->createOrderMerchantName = $merchant->name;
        $this->createOrderItemCount = $items->count();
        $this->createOrderTotalAmount = $totalAmount;
        $this->showCreateOrderConfirm = true;
    }

    /**
     * 执行生成订单：按商户创建订单 + 明细，清空已下单的购物车项
     */
    public function executeCreateOrder(): void
    {
        if (!$this->createOrderMerchantId) {
            $this->toastError('商家ID无效');
            return;
        }

        $merchant = Merchant::find($this->createOrderMerchantId);
        $cart = Cart::where('merchant_id', $this->createOrderMerchantId)->first();

        if (!$cart) {
            $this->toastError('购物车不存在');
            $this->showCreateOrderConfirm = false;
            return;
        }

        $cartItems = CartItem::where('cart_id', $cart->id)->with('sku.product')->get();
        if ($cartItems->isEmpty()) {
            $this->toastError('购物车为空，无法生成订单');
            $this->showCreateOrderConfirm = false;
            return;
        }

        try {
            $totalAmount = $cartItems->sum(fn($item) => $item->quantity * $item->price);

            // 创建订单
            $order = Order::create([
                'order_no' => Order::generateOrderNo(),
                'merchant_id' => $this->createOrderMerchantId,
                'status' => Order::STATUS_PICKING_WAIT,
                'total_amount' => $totalAmount,
                'adjusted_amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'payment_status' => Order::PAYMENT_UNPAID,
                'settlement_type' => $merchant->settlement_type ?: Order::SETTLEMENT_CASH,
                'delivery_address' => $merchant->address ?: null,
                'contact_name' => $merchant->contact_name ?: null,
                'contact_phone' => $merchant->contact_phone ?: null,
                'order_date' => now()->toDateString(),
                'delivery_date' => now()->addDay()->toDateString(),
                'is_locked' => 0,
                'remark' => null,
            ]);

            // 创建订单明细
            foreach ($cartItems as $cartItem) {
                $subtotal = $cartItem->quantity * $cartItem->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'sku_id' => $cartItem->sku_id,
                    'product_name' => $cartItem->sku?->product?->name ?? '',
                    'sku_specs' => $cartItem->sku?->specs ?? null,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'actual_quantity' => $cartItem->quantity,
                    'actual_price' => $cartItem->price,
                    'subtotal' => $subtotal,
                    'actual_subtotal' => $subtotal,
                    'unit_id' => $cartItem->unit_id,
                    'unit_quantity' => $cartItem->unit_quantity,
                    'strategy_price' => 0,
                    'strategy_amount' => 0,
                    'discrepancy_amount' => 0,
                    'status' => OrderItem::STATUS_NORMAL,
                ]);
            }

            // 审计日志
            AuditLog::log(
                modelType: Order::class,
                modelId: $order->id,
                action: 'create',
                afterData: $order->toArray(),
            );

            // 清空该商户的购物车项
            CartItem::where('cart_id', $cart->id)->delete();

            $this->showCreateOrderConfirm = false;
            $this->toastSuccess("订单 {$order->order_no} 已生成，共 {$cartItems->count()} 条明细");
        } catch (\Exception $e) {
            $this->toastError('生成订单失败：' . $e->getMessage());
        }
    }

    public function closeCreateOrderConfirm(): void
    {
        $this->showCreateOrderConfirm = false;
    }

    private function buildQuery()
    {
        $query = CartItem::with(['cart.merchant', 'sku.product'])->orderBy('id', 'desc');

        if ($this->filterMerchantId) {
            $query->whereHas('cart', function ($q) {
                $q->where('merchant_id', $this->filterMerchantId);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('cart.merchant', function ($mq) {
                    $mq->where('name', 'like', "%{$this->search}%");
                })->orWhereHas('sku', function ($sq) {
                    $sq->where('sku_code', 'like', "%{$this->search}%")
                        ->orWhereHas('product', function ($pq) {
                            $pq->where('name', 'like', "%{$this->search}%");
                        });
                });
            });
        }

        return $query;
    }

    /**
     * 按商户分组获取购物车数据（用于分组展示）
     */
    private function getGroupedByMerchant($cartItems)
    {
        return $cartItems->groupBy(function ($item) {
            return $item->cart?->merchant_id ?? 0;
        })->map(function ($items, $merchantId) {
            $merchant = $items->first()->cart?->merchant;
            $totalAmount = $items->sum(fn($item) => $item->quantity * $item->price);
            return [
                'merchant_id' => $merchantId,
                'merchant_name' => $merchant?->name ?? '未知商家',
                'items' => $items,
                'item_count' => $items->count(),
                'total_amount' => $totalAmount,
            ];
        })->sortBy('merchant_name');
    }

    public function render()
    {
        $items = $this->buildQuery()->paginate(setting('per_page', 10));
        $merchants = Merchant::where('status', 1)->orderBy('name')->get();
        $skus = Sku::with('product')->orderBy('sku_code')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        // 按商户分组
        $groupedItems = $this->getGroupedByMerchant($items->getCollection());

        return view('livewire.order.cart-list', compact('items', 'merchants', 'skus', 'allColumns', 'selectedCount', 'groupedItems', 'availableUnits', 'unitPreview'))
            ->layout('components.app-layout')
            ->title('购物车');
    }
}
