<?php

use App\Livewire\Order\OrderReturnList;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Sku;
use App\Services\OrderReturnService;
use Livewire\Livewire;

/**
 * 售后退货流程测试（头先行 + 详情页维护明细，与采购退货同模式）
 *
 * 金额单位：厘（1 元 = 1000 厘）
 */
function createOrderReturnFixtures(): array
{
    $sku = Sku::create(['product_id' => 1, 'sku_code' => 'ORET-TEST-001', 'status' => 1]);
    $order = Order::create([
        'order_no' => 'ORD-RET-'.uniqid(),
        'merchant_id' => 1,
        'status' => Order::STATUS_SIGNED,
    ]);
    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'sku_id' => $sku->id,
        'product_name' => '测试商品',
        'quantity' => 600,
        'price' => 13800,
        'actual_quantity' => 500, // 实收 500（签收差异）
        'actual_price' => 13800,
    ]);

    return compact('sku', 'order', 'orderItem');
}

it('creates a header-only return with merchant taken from the order', function () {
    ['order' => $order] = createOrderReturnFixtures();

    $return = app(OrderReturnService::class)->create($order, [], '先存单头');

    expect($return->return_no)->toStartWith('RT-')
        ->and($return->merchant_id)->toBe($order->merchant_id)
        ->and($return->status)->toBe(OrderReturn::STATUS_PENDING)
        ->and($return->items)->toHaveCount(0)
        ->and($return->total_amount)->toBe(0);
});

it('rejects creating returns for orders not signed', function () {
    ['order' => $order] = createOrderReturnFixtures();
    $order->update(['status' => Order::STATUS_DELIVERING]);

    app(OrderReturnService::class)->create($order->fresh(), []);
})->throws(Exception::class, '仅已签收的订单可创建售后退货');

it('prices items from actual receipt and caps quantity at received amount', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createOrderReturnFixtures();
    $service = app(OrderReturnService::class);

    // 实收 500，退 600 应被拦截
    expect(fn () => $service->create($order, [
        ['order_item_id' => $orderItem->id, 'quantity' => 600],
    ]))->toThrow(Exception::class, '不能大于实收数量');

    $return = $service->create($order, [
        ['order_item_id' => $orderItem->id, 'quantity' => 200, 'reason' => '品质问题'],
    ], '部分退货');

    expect($return->total_amount)->toBe(2760000) // 200 × 13800 厘
        ->and($return->items->first()->price)->toBe(13800);
});

it('replaces items via updateItems and blocks edits after approval', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createOrderReturnFixtures();
    $service = app(OrderReturnService::class);

    $return = $service->create($order, [
        ['order_item_id' => $orderItem->id, 'quantity' => 100],
    ]);
    expect($return->total_amount)->toBe(1380000);

    $return = $service->updateItems($return, [
        ['order_item_id' => $orderItem->id, 'quantity' => 300, 'reason' => '改数量'],
    ]);
    expect($return->items)->toHaveCount(1)
        ->and($return->total_amount)->toBe(4140000);

    $service->approve($return);
    expect(fn () => $service->updateItems($return->fresh(), [
        ['order_item_id' => $orderItem->id, 'quantity' => 10],
    ]))->toThrow(Exception::class, '仅待审核状态的退货单可编辑明细');
});

it('rejects approving a return without items', function () {
    ['order' => $order] = createOrderReturnFixtures();

    $return = app(OrderReturnService::class)->create($order, []);
    app(OrderReturnService::class)->approve($return);
})->throws(Exception::class, '请先添加退货明细再审核');

it('runs the full flow: approve → returned → refund with synced amounts', function () {
    ['order' => $order, 'orderItem' => $orderItem] = createOrderReturnFixtures();
    $service = app(OrderReturnService::class);

    $return = $service->create($order, [
        ['order_item_id' => $orderItem->id, 'quantity' => 500],
    ], '整单退货');

    $return = $service->approve($return);
    expect($return->status)->toBe(OrderReturn::STATUS_APPROVED)
        ->and($return->audited_at)->not->toBeNull();

    $return = $service->markReturned($return);
    expect($return->status)->toBe(OrderReturn::STATUS_RETURNED)
        ->and($return->returned_at)->not->toBeNull();

    $return = $service->refund($return);
    expect($return->status)->toBe(OrderReturn::STATUS_REFUNDED)
        ->and($return->refund_amount)->toBe(6900000) // = total_amount
        ->and($return->refunded_at)->not->toBeNull()
        ->and($return->items->first()->refund_amount)->toBe(6900000);

    // 已退款不可作废
    expect(fn () => $service->cancel($return))->toThrow(Exception::class, '仅待审核/已审核状态的退货单可作废');
});

it('creates a header-only return from the list component and redirects to detail page', function () {
    ['order' => $order] = createOrderReturnFixtures();

    Livewire::test(OrderReturnList::class)
        ->set('formOrderId', $order->id)
        ->set('formReason', '土豆质量问题')
        ->call('save')
        ->assertRedirectContains('order-returns/');

    $return = OrderReturn::where('order_id', $order->id)->latest('id')->first();
    expect($return->status)->toBe(OrderReturn::STATUS_PENDING)
        ->and($return->items)->toHaveCount(0);
});
