<?php

use App\Livewire\Finance\SupplierSettlementList;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\SettlementPayment;
use App\Models\Supplier;
use App\Models\SupplierSettlement;
use App\Models\User;
use App\Services\SupplierSettlementService;
use Livewire\Livewire;

function createSettlementFixtures(): array
{
    $supplier = Supplier::create(['name' => '测试供应商']);

    // 周期内：已入库（含入库差异，实入 900 元 < 订 1000 元）
    $orderStocked = PurchaseOrder::create([
        'order_no' => 'PO-'.uniqid(), 'supplier_id' => $supplier->id,
        'status' => PurchaseOrder::STATUS_STOCKED,
        'total_amount' => 100000, 'actual_amount' => 90000,
        'stocked_at' => '2026-08-10 10:00:00',
    ]);
    // 周期内：已完成（无差异）
    $orderCompleted = PurchaseOrder::create([
        'order_no' => 'PO-'.uniqid(), 'supplier_id' => $supplier->id,
        'status' => PurchaseOrder::STATUS_COMPLETED,
        'total_amount' => 50000, 'actual_amount' => 50000,
        'stocked_at' => '2026-08-15 10:00:00',
    ]);
    // 周期外已入库：不应计入
    PurchaseOrder::create([
        'order_no' => 'PO-'.uniqid(), 'supplier_id' => $supplier->id,
        'status' => PurchaseOrder::STATUS_STOCKED,
        'total_amount' => 70000, 'actual_amount' => 70000,
        'stocked_at' => '2026-07-01 10:00:00',
    ]);
    // 周期内但未入库（已发货）：不应计入
    PurchaseOrder::create([
        'order_no' => 'PO-'.uniqid(), 'supplier_id' => $supplier->id,
        'status' => PurchaseOrder::STATUS_SHIPPED,
        'total_amount' => 30000, 'actual_amount' => 0,
    ]);
    // 周期内已完成退货：实际出库 100 元，应扣减
    PurchaseReturn::create([
        'return_no' => 'PR-'.uniqid(), 'purchase_order_id' => $orderStocked->id,
        'supplier_id' => $supplier->id, 'warehouse_id' => 1,
        'status' => PurchaseReturn::STATUS_COMPLETED,
        'total_amount' => 10000, 'actual_amount' => 10000,
        'completed_at' => '2026-08-12 10:00:00',
    ]);

    return compact('supplier', 'orderStocked', 'orderCompleted');
}

it('generates period settlement with actual amounts minus returns and service fee', function () {
    ['supplier' => $supplier] = createSettlementFixtures();

    $settlement = app(SupplierSettlementService::class)->generate(
        supplierId: $supplier->id,
        startDate: '2026-08-01',
        endDate: '2026-08-31',
        serviceFee: 5000,
    );

    // 应付 = (90000 + 50000) - 10000 - 5000 = 125000；入库差异按实收计价天然持平
    expect($settlement->total_amount)->toBe(140000)
        ->and($settlement->return_amount)->toBe(10000)
        ->and($settlement->service_fee)->toBe(5000)
        ->and($settlement->payable_amount)->toBe(125000)
        ->and($settlement->status)->toBe(SupplierSettlement::STATUS_PENDING)
        ->and($settlement->items)->toHaveCount(2);
});

it('rejects regenerating settlement for already settled orders', function () {
    ['supplier' => $supplier] = createSettlementFixtures();

    $service = app(SupplierSettlementService::class);
    $service->generate($supplier->id, '2026-08-01', '2026-08-31');

    // 周期内采购单已全部结算，再次生成应报错
    $service->generate($supplier->id, '2026-08-01', '2026-08-31');
})->throws(Exception::class, '没有可结算');

it('rejects generation when no stocked orders in period', function () {
    ['supplier' => $supplier] = createSettlementFixtures();

    app(SupplierSettlementService::class)->generate($supplier->id, '2026-09-01', '2026-09-30');
})->throws(Exception::class, '没有可结算');

it('creates settlement through the list component form', function () {
    ['supplier' => $supplier] = createSettlementFixtures();

    Livewire::test(SupplierSettlementList::class)
        ->set('formSupplierId', $supplier->id)
        ->set('formStartDate', '2026-08-01')
        ->set('formEndDate', '2026-08-31')
        ->set('formServiceFee', '5') // 5 元 = 5000 厘
        ->call('save');

    $settlement = SupplierSettlement::where('supplier_id', $supplier->id)->first();

    expect($settlement)->not->toBeNull()
        ->and($settlement->payable_amount)->toBe(125000)
        ->and($settlement->items)->toHaveCount(2);
});

it('settles via partial payments with payment records', function () {
    ['supplier' => $supplier] = createSettlementFixtures();
    $user = User::create(['username' => 'tester'.uniqid(), 'name' => '测试员', 'password' => bcrypt('secret')]);
    $this->actingAs($user);

    $settlement = app(SupplierSettlementService::class)->generate($supplier->id, '2026-08-01', '2026-08-31');

    // 应付 130000 厘 = 130 元。先付 50 元 → 部分付款
    Livewire::test(SupplierSettlementList::class)
        ->call('openPartialPayment', $settlement->id)
        ->set('paymentAmount', '50')
        ->call('submitPartialPayment');

    $settlement->refresh();
    expect($settlement->paid_amount)->toBe(50000)
        ->and($settlement->status)->toBe(SupplierSettlement::STATUS_PARTIAL);

    // 超付拦截：剩余 80 元，付 90 元应被拒
    Livewire::test(SupplierSettlementList::class)
        ->call('openPartialPayment', $settlement->id)
        ->set('paymentAmount', '90')
        ->call('submitPartialPayment');

    expect($settlement->fresh()->paid_amount)->toBe(50000);

    // 付清剩余 80 元 → 已结清，付款记录共 2 条
    Livewire::test(SupplierSettlementList::class)
        ->call('openPartialPayment', $settlement->id)
        ->set('paymentAmount', '80')
        ->call('submitPartialPayment');

    $settlement->refresh();
    expect($settlement->status)->toBe(SupplierSettlement::STATUS_SETTLED)
        ->and($settlement->settled_at)->not->toBeNull()
        ->and(SettlementPayment::where('settlement_id', $settlement->id)->count())->toBe(2);
});

it('only allows deleting pending settlements', function () {
    ['supplier' => $supplier] = createSettlementFixtures();
    $user = User::create(['username' => 'tester'.uniqid(), 'name' => '测试员', 'password' => bcrypt('secret')]);
    $this->actingAs($user);

    $settlement = app(SupplierSettlementService::class)->generate($supplier->id, '2026-08-01', '2026-08-31');

    // 部分付款后不可删除
    $settlement->update(['status' => SupplierSettlement::STATUS_PARTIAL, 'paid_amount' => 100]);
    Livewire::test(SupplierSettlementList::class)
        ->set('deletingId', $settlement->id)
        ->call('delete');
    expect(SupplierSettlement::withTrashed()->find($settlement->id)->deleted_at)->toBeNull();

    // 待结算可删除，且结算明细一并清理
    $settlement->update(['status' => SupplierSettlement::STATUS_PENDING, 'paid_amount' => 0]);
    Livewire::test(SupplierSettlementList::class)
        ->set('deletingId', $settlement->id)
        ->call('delete');

    expect(SupplierSettlement::find($settlement->id))->toBeNull()
        ->and($settlement->items()->count())->toBe(0);
});
