<?php

use App\Models\Discrepancy;
use App\Models\Inventory;
use App\Models\LossOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sku;
use App\Models\Warehouse;
use App\Services\DiscrepancyService;
use App\Services\InventoryService;
use App\Services\LossOrderService;

function createDiscrepancyFixtures(): array
{
    $warehouse = Warehouse::create(['name' => '总仓', 'type' => 1, 'status' => 1]);
    $sku = Sku::create(['product_id' => 1, 'sku_code' => 'DIS-TEST-001', 'status' => 1]);
    $order = Order::create(['order_no' => 'TEST-'.uniqid(), 'merchant_id' => 1, 'status' => 1]);
    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'sku_id' => $sku->id,
        'product_name' => '测试商品',
        'quantity' => 10,
        'price' => 100,
    ]);

    return compact('warehouse', 'sku', 'order', 'orderItem');
}

it('creates a discrepancy with auto computed diffs', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createDiscrepancyFixtures();

    $discrepancy = app(DiscrepancyService::class)->create([
        'order_id' => $order->id,
        'order_item_id' => $orderItem->id,
        'stage' => Discrepancy::STAGE_PICKING,
        'type' => Discrepancy::TYPE_SHORT,
        'expected_quantity' => 10,
        'actual_quantity' => 7,
        'reason' => '库存不足',
    ]);

    expect($discrepancy->discrepancy_no)->toStartWith('DIS-')
        ->and($discrepancy->quantity_diff)->toBe(3)
        ->and($discrepancy->amount_diff)->toBe(300)
        ->and($discrepancy->status)->toBe(Discrepancy::STATUS_PENDING);
});

it('rejects duplicate open discrepancy for same item and stage', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createDiscrepancyFixtures();
    $service = app(DiscrepancyService::class);

    $payload = [
        'order_id' => $order->id,
        'order_item_id' => $orderItem->id,
        'stage' => Discrepancy::STAGE_PICKING,
        'type' => Discrepancy::TYPE_SHORT,
        'expected_quantity' => 10,
        'actual_quantity' => 7,
    ];

    $service->create($payload);
    $service->create($payload);
})->throws(Exception::class, '请勿重复登记');

it('creates a pending loss order when decision is loss', function () {
    ['warehouse' => $warehouse, 'orderItem' => $orderItem] = createDiscrepancyFixtures();

    $discrepancy = app(DiscrepancyService::class)->create([
        'order_item_id' => $orderItem->id,
        'stage' => Discrepancy::STAGE_RECEIVING,
        'type' => Discrepancy::TYPE_DEFECT,
        'expected_quantity' => 10,
        'actual_quantity' => 8,
        'reason' => '破损',
    ]);

    $processed = app(DiscrepancyService::class)->process(
        discrepancy: $discrepancy,
        responsibleParty: Discrepancy::PARTY_PLATFORM,
        decision: Discrepancy::DECISION_LOSS,
        warehouseId: $warehouse->id,
    );

    $lossOrder = LossOrder::where('source_type', 'discrepancy')->where('source_id', $discrepancy->id)->first();

    expect($processed->status)->toBe(Discrepancy::STATUS_RESOLVED)
        ->and($lossOrder)->not->toBeNull()
        ->and($lossOrder->status)->toBe(LossOrder::STATUS_PENDING)
        ->and($lossOrder->items)->toHaveCount(1)
        ->and($lossOrder->items->first()->quantity)->toBe(2);
});

it('deducts inventory through the full loss chain: discrepancy → loss order → approve → execute', function () {
    ['warehouse' => $warehouse, 'sku' => $sku, 'orderItem' => $orderItem] = createDiscrepancyFixtures();

    // 先入库 10 件，作为报损扣减的基数
    app(InventoryService::class)->stockIn(
        warehouse: $warehouse->id,
        sku: $sku->id,
        quantity: 10,
        batchNo: 'B-001',
        sourceType: 'test',
        sourceId: 1,
        reason: '测试入库',
    );

    $discrepancy = app(DiscrepancyService::class)->create([
        'order_item_id' => $orderItem->id,
        'stage' => Discrepancy::STAGE_RECEIVING,
        'type' => Discrepancy::TYPE_DEFECT,
        'expected_quantity' => 10,
        'actual_quantity' => 8,
        'reason' => '破损',
    ]);

    app(DiscrepancyService::class)->process(
        discrepancy: $discrepancy,
        responsibleParty: Discrepancy::PARTY_PLATFORM,
        decision: Discrepancy::DECISION_LOSS,
        warehouseId: $warehouse->id,
    );

    $lossOrder = LossOrder::where('source_type', 'discrepancy')->where('source_id', $discrepancy->id)->firstOrFail();

    $lossOrderService = app(LossOrderService::class);
    $lossOrderService->approve($lossOrder);
    $lossOrderService->execute($lossOrder->fresh());

    $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('sku_id', $sku->id)->first();

    expect($lossOrder->fresh()->status)->toBe(LossOrder::STATUS_EXECUTED)
        ->and($inventory->total_stock)->toBe(8);
});

it('closes discrepancy directly when decision is ignore', function () {
    ['orderItem' => $orderItem] = createDiscrepancyFixtures();

    $discrepancy = app(DiscrepancyService::class)->create([
        'order_item_id' => $orderItem->id,
        'stage' => Discrepancy::STAGE_PICKING,
        'type' => Discrepancy::TYPE_OTHER,
        'expected_quantity' => 5,
        'actual_quantity' => 4,
    ]);

    $processed = app(DiscrepancyService::class)->process(
        discrepancy: $discrepancy,
        responsibleParty: Discrepancy::PARTY_PLATFORM,
        decision: Discrepancy::DECISION_IGNORE,
    );

    expect($processed->status)->toBe(Discrepancy::STATUS_CLOSED);
    expect(LossOrder::where('source_type', 'discrepancy')->where('source_id', $discrepancy->id)->exists())->toBeFalse();
});

it('restocks rejected goods back to warehouse inventory', function () {
    ['warehouse' => $warehouse, 'sku' => $sku, 'orderItem' => $orderItem] = createDiscrepancyFixtures();

    $discrepancy = app(DiscrepancyService::class)->create([
        'order_item_id' => $orderItem->id,
        'stage' => Discrepancy::STAGE_RECEIVING,
        'type' => Discrepancy::TYPE_REJECT,
        'expected_quantity' => 10,
        'actual_quantity' => 6,
        'reason' => '商家拒收',
    ]);

    $processed = app(DiscrepancyService::class)->restockToWarehouse($discrepancy, $warehouse->id);

    $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('sku_id', $sku->id)->first();

    expect($processed->status)->toBe(Discrepancy::STATUS_RESOLVED)
        ->and($inventory)->not->toBeNull()
        ->and($inventory->total_stock)->toBe(4);
});

it('refuses to process an already resolved discrepancy', function () {
    ['orderItem' => $orderItem] = createDiscrepancyFixtures();

    $discrepancy = app(DiscrepancyService::class)->create([
        'order_item_id' => $orderItem->id,
        'stage' => Discrepancy::STAGE_PICKING,
        'type' => Discrepancy::TYPE_SHORT,
        'expected_quantity' => 5,
        'actual_quantity' => 4,
    ]);

    $service = app(DiscrepancyService::class);
    $service->process($discrepancy, Discrepancy::PARTY_PLATFORM, Discrepancy::DECISION_IGNORE);

    $service->process($discrepancy->fresh(), Discrepancy::PARTY_PLATFORM, Discrepancy::DECISION_IGNORE);
})->throws(Exception::class, '仅待处理');
