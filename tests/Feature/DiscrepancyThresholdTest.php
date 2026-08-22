<?php

use App\Models\LossOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Sku;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use App\Support\Setting;

function createPurchaseStockInFixtures(int $quantity = 10): array
{
    $warehouse = Warehouse::create(['name' => '总仓', 'type' => 1, 'status' => 1]);
    $sku = Sku::create(['product_id' => 1, 'sku_code' => 'TH-'.uniqid(), 'status' => 1]);
    $order = PurchaseOrder::create([
        'order_no' => 'PO-'.uniqid(),
        'supplier_id' => 1,
        'status' => PurchaseOrder::STATUS_SHIPPED,
        'total_amount' => $quantity * 100,
    ]);
    $item = PurchaseOrderItem::create([
        'purchase_order_id' => $order->id,
        'sku_id' => $sku->id,
        'quantity' => $quantity,
        'price' => 100,
        'amount' => $quantity * 100,
    ]);

    return compact('warehouse', 'sku', 'order', 'item');
}

function stockInPayload(PurchaseOrderItem $item, int $actualQuantity): array
{
    return [[
        'id' => $item->id,
        'actual_quantity' => $actualQuantity,
        'actual_price' => 100,
        'discrepancy_reason' => $actualQuantity < $item->quantity ? '供应商少送' : null,
    ]];
}

it('blocks stock-in discrepancy in strict mode', function () {
    ['warehouse' => $warehouse, 'order' => $order, 'item' => $item] = createPurchaseStockInFixtures(10);

    Setting::set('purchase_allow_discrepancy', false);

    app(PurchaseService::class)->stockIn($order, $warehouse->id, stockInPayload($item, 9), 'B-001');
})->throws(Exception::class, '收发一致');

it('records discrepancy without loss order when within tolerance threshold', function () {
    ['warehouse' => $warehouse, 'order' => $order, 'item' => $item] = createPurchaseStockInFixtures(10);

    Setting::set('purchase_allow_discrepancy', true);
    Setting::set('purchase_discrepancy_threshold', 10);

    app(PurchaseService::class)->stockIn($order, $warehouse->id, stockInPayload($item, 9), 'B-001');

    $item->refresh();

    // 差异率 10% 等于阈值：只留痕记录，不生成损耗单
    expect($item->discrepancy_quantity)->toBe(1)
        ->and($item->loss_order_id)->toBeNull()
        ->and(LossOrder::where('source_type', 'purchase_order')->where('source_id', $order->id)->exists())->toBeFalse();

    // 阈值内留痕的差异不阻断采购单完成
    $completed = app(PurchaseService::class)->complete($order->fresh());
    expect($completed->status)->toBe(PurchaseOrder::STATUS_COMPLETED);
});

it('creates loss order when discrepancy exceeds tolerance threshold', function () {
    ['warehouse' => $warehouse, 'order' => $order, 'item' => $item] = createPurchaseStockInFixtures(10);

    Setting::set('purchase_allow_discrepancy', true);
    Setting::set('purchase_discrepancy_threshold', 10);

    // 差异率 20% 超过阈值 10%：自动生成损耗单
    app(PurchaseService::class)->stockIn($order, $warehouse->id, stockInPayload($item, 8), 'B-001');

    $lossOrder = LossOrder::where('source_type', 'purchase_order')->where('source_id', $order->id)->first();

    expect($lossOrder)->not->toBeNull()
        ->and($lossOrder->items)->toHaveCount(1)
        ->and($lossOrder->items->first()->quantity)->toBe(2)
        ->and($item->fresh()->loss_order_id)->toBe($lossOrder->id);
});

it('defaults to loose mode with zero threshold from seeded configs', function () {
    expect(setting('purchase_allow_discrepancy', null))->toBeTrue()
        ->and(setting('purchase_discrepancy_threshold', null))->toBe(0)
        ->and(setting('picking_allow_discrepancy', null))->toBeTrue()
        ->and(setting('picking_discrepancy_threshold', null))->toBe(0)
        ->and(setting('delivery_allow_discrepancy', null))->toBeTrue()
        ->and(setting('receiving_allow_discrepancy', null))->toBeTrue()
        ->and(setting('receiving_discrepancy_threshold', null))->toBe(0);
});
