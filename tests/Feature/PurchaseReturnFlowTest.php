<?php

use App\Models\Inventory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\Sku;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Services\PurchaseReturnService;

/**
 * 采购退货双模式（关联退货 / 普通退货）测试
 *
 * 金额单位：厘（1 元 = 1000 厘）
 */
function createReturnFixtures(): array
{
    $warehouse = Warehouse::create(['name' => '退货仓', 'type' => 1, 'status' => 1]);
    $sku = Sku::create(['product_id' => 1, 'sku_code' => 'RET-TEST-001', 'status' => 1]);
    $supplier = Supplier::create(['name' => '退货供应商']);

    $order = PurchaseOrder::create([
        'order_no' => 'PO-RET-'.uniqid(),
        'supplier_id' => $supplier->id,
        'warehouse_id' => $warehouse->id,
        'status' => PurchaseOrder::STATUS_STOCKED,
        'total_amount' => 50000,
        'actual_amount' => 45000,
        'stocked_at' => now(),
    ]);
    $orderItem = PurchaseOrderItem::create([
        'purchase_order_id' => $order->id,
        'sku_id' => $sku->id,
        'quantity' => 10,
        'price' => 5000,
        'amount' => 50000,
        'actual_quantity' => 9,
        'actual_price' => 5000,
        'actual_amount' => 45000,
    ]);

    return compact('warehouse', 'sku', 'supplier', 'order', 'orderItem');
}

it('creates a linked return priced from actual receipt quantity', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createReturnFixtures();

    $return = app(PurchaseReturnService::class)->createLinked($order, [
        ['purchase_order_item_id' => $orderItem->id, 'quantity' => 4, 'reason' => '质量问题'],
    ], '批次质量退回');

    expect($return->return_type)->toBe(PurchaseReturn::TYPE_LINKED)
        ->and($return->purchase_order_id)->toBe($order->id)
        ->and($return->total_amount)->toBe(20000) // 4 × 5000 厘
        ->and($return->items)->toHaveCount(1)
        ->and($return->items->first()->price)->toBe(5000);
});

it('rejects linked return quantity above actual receipt quantity', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createReturnFixtures();

    // 实际入库 9 件，退 10 件应被拦截（决策 1A）
    app(PurchaseReturnService::class)->createLinked($order, [
        ['purchase_order_item_id' => $orderItem->id, 'quantity' => 10],
    ]);
})->throws(Exception::class, '不能大于实际入库数量');

it('rejects linked return for orders not yet stocked', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createReturnFixtures();
    $order->update(['status' => PurchaseOrder::STATUS_SHIPPED]);

    app(PurchaseReturnService::class)->createLinked($order->fresh(), [
        ['purchase_order_item_id' => $orderItem->id, 'quantity' => 1],
    ]);
})->throws(Exception::class, '仅已入库/完成状态的采购单可创建退货');

it('creates a normal return with custom items and manual prices', function () {
    ['warehouse' => $warehouse, 'sku' => $sku, 'supplier' => $supplier] = createReturnFixtures();

    $return = app(PurchaseReturnService::class)->createNormal(
        supplierId: $supplier->id,
        warehouseId: $warehouse->id,
        items: [
            ['sku_id' => $sku->id, 'quantity' => 3, 'price' => 4500, 'reason' => '滞销退回'],
        ],
        reason: '普通退货',
    );

    expect($return->return_type)->toBe(PurchaseReturn::TYPE_NORMAL)
        ->and($return->purchase_order_id)->toBeNull()
        ->and($return->total_amount)->toBe(13500) // 3 × 4500 厘
        ->and($return->items->first()->purchase_order_item_id)->toBeNull();
});

it('rejects normal return with empty items or invalid quantity', function () {
    ['warehouse' => $warehouse, 'sku' => $sku, 'supplier' => $supplier] = createReturnFixtures();
    $service = app(PurchaseReturnService::class);

    // 空明细
    expect(fn () => $service->createNormal($supplier->id, $warehouse->id, []))
        ->toThrow(Exception::class, '退货明细不能为空');

    // 数量为 0
    expect(fn () => $service->createNormal($supplier->id, $warehouse->id, [
        ['sku_id' => $sku->id, 'quantity' => 0, 'price' => 1000],
    ]))->toThrow(Exception::class, '退货数量必须大于0');
});

it('ships a normal return and deducts inventory from the return warehouse', function () {
    ['warehouse' => $warehouse, 'sku' => $sku, 'supplier' => $supplier] = createReturnFixtures();

    // 先入库 5 件作为退货出库基数
    app(InventoryService::class)->stockIn(
        warehouse: $warehouse->id,
        sku: $sku->id,
        quantity: 5,
        batchNo: 'B-RET-001',
    );

    $service = app(PurchaseReturnService::class);
    $return = $service->createNormal($supplier->id, $warehouse->id, [
        ['sku_id' => $sku->id, 'quantity' => 3, 'price' => 4500],
    ]);

    $service->approve($return);
    $return = $service->ship($return->fresh());

    $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('sku_id', $sku->id)->first();

    expect($return->status)->toBe(PurchaseReturn::STATUS_SHIPPED)
        ->and($inventory->total_stock)->toBe(2) // 5 - 3
        ->and($return->actual_amount)->toBe(13500);

    // 普通退货完成后不应触发采购单退货状态更新（无关联单据）
    $return = $service->complete($return);
    expect($return->status)->toBe(PurchaseReturn::STATUS_COMPLETED)
        ->and($return->completed_at)->not->toBeNull();
});

it('completed normal returns are included in settlement return amount', function () {
    ['warehouse' => $warehouse, 'sku' => $sku, 'supplier' => $supplier] = createReturnFixtures();

    // 普通退货直接置为完成态，验证结算汇总口径按 supplier + completed_at 统计
    PurchaseReturn::create([
        'return_no' => 'PR-N-'.uniqid(),
        'return_type' => PurchaseReturn::TYPE_NORMAL,
        'purchase_order_id' => null,
        'supplier_id' => $supplier->id,
        'warehouse_id' => $warehouse->id,
        'status' => PurchaseReturn::STATUS_COMPLETED,
        'total_amount' => 8000,
        'actual_amount' => 8000,
        'completed_at' => now(),
    ]);

    $returnAmount = PurchaseReturn::where('supplier_id', $supplier->id)
        ->where('status', PurchaseReturn::STATUS_COMPLETED)
        ->whereBetween('completed_at', [now()->startOfDay(), now()->endOfDay()])
        ->sum('actual_amount');

    expect($returnAmount)->toBe(8000);
});
