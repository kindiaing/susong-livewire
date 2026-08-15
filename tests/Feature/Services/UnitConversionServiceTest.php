<?php

use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\Sku;
use App\Services\UnitConversionService;

function createDefaultUnits(): void
{
    $units = [
        ['name' => '箱', 'symbol' => 'X', 'sort' => 1],
        ['name' => '件', 'symbol' => 'J', 'sort' => 2],
        ['name' => '包', 'symbol' => 'B', 'sort' => 3],
        ['name' => '斤', 'symbol' => 'JIN', 'sort' => 4],
        ['name' => '桶', 'symbol' => 'T', 'sort' => 5],
        ['name' => '袋', 'symbol' => 'D', 'sort' => 6],
        ['name' => '盒', 'symbol' => 'H', 'sort' => 7],
        ['name' => '瓶', 'symbol' => 'P', 'sort' => 8],
        ['name' => '个', 'symbol' => 'G', 'sort' => 9],
        ['name' => '条', 'symbol' => 'TIA', 'sort' => 10],
    ];

    foreach ($units as $unit) {
        Unit::firstOrCreate(
            ['name' => $unit['name']],
            ['symbol' => $unit['symbol'], 'status' => 1, 'sort' => $unit['sort']]
        );
    }
}

function createTestSkuWithChain(int $productId = 1, string $skuCode = 'TEST-001', array $conversions = [[1, 2, 6], [2, 3, 10]]): array
{
    createDefaultUnits();

    $sku = Sku::create([
        'product_id' => $productId,
        'sku_code' => $skuCode,
        'base_unit_id' => $conversions[count($conversions) - 1][1], // 最后一级的 to_unit 是最小单位
        'status' => 1,
    ]);

    $parentConversionId = null;
    foreach ($conversions as $i => [$fromUnitId, $toUnitId, $ratio]) {
        $conversion = UnitConversion::create([
            'sku_id' => $sku->id,
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => $toUnitId,
            'ratio' => $ratio,
            'parent_conversion_id' => $parentConversionId,
            'status' => 1,
            'sort' => $i,
        ]);
        $parentConversionId = $conversion->id;
    }

    return ['sku' => $sku];
}

it('can format human quantity with three level chain', function () {
    ['sku' => $sku] = createTestSkuWithChain(1, 'TEST-001', [[1, 2, 6], [2, 3, 10]]);
    $service = new UnitConversionService();

    expect($service->formatHuman($sku->id, 130))->toBe('2箱1件10包')
        ->and($service->formatHuman($sku->id, 60))->toBe('1箱')
        ->and($service->formatHuman($sku->id, 10))->toBe('1件')
        ->and($service->formatHuman($sku->id, 7))->toBe('7包')
        ->and($service->formatHuman($sku->id, 0))->toBe('0')
        ->and($service->formatHuman($sku->id, 66))->toBe('1箱1件');
});

it('can convert to base unit', function () {
    ['sku' => $sku] = createTestSkuWithChain(1, 'TEST-002', [[1, 2, 6], [2, 3, 10]]);
    $service = new UnitConversionService();

    expect($service->convertToBase($sku->id, 1, 2))->toBe(120)   // 2箱=120包
        ->and($service->convertToBase($sku->id, 2, 5))->toBe(50)  // 5件=50包
        ->and($service->convertToBase($sku->id, 3, 7))->toBe(7)   // 7包=7
        ->and($service->convertToBase($sku->id, 1, 0))->toBe(0);
});

it('can convert from base unit', function () {
    ['sku' => $sku] = createTestSkuWithChain(1, 'TEST-003', [[1, 2, 6], [2, 3, 10]]);
    $service = new UnitConversionService();

    expect($service->convertFromBase($sku->id, 1, 120))->toBe(2)  // 120包=2箱
        ->and($service->convertFromBase($sku->id, 2, 50))->toBe(5) // 50包=5件
        ->and($service->convertFromBase($sku->id, 3, 7))->toBe(7);
});

it('can convert between any two units', function () {
    ['sku' => $sku] = createTestSkuWithChain(1, 'TEST-004', [[1, 2, 6], [2, 3, 10]]);
    $service = new UnitConversionService();

    expect($service->convert($sku->id, 1, 2, 1))->toBe(6.0)      // 1箱=6件
        ->and($service->convert($sku->id, 1, 3, 2))->toBe(120.0)  // 2箱=120包
        ->and($service->convert($sku->id, 2, 3, 5))->toBe(50.0)  // 5件=50包
        ->and($service->convert($sku->id, 3, 2, 10))->toBe(1.0)  // 10包=1件
        ->and($service->convert($sku->id, 3, 1, 60))->toBe(1.0); // 60包=1箱
});

it('returns empty chain when no conversions exist', function () {
    createDefaultUnits();
    $sku = Sku::create(['product_id' => 1, 'sku_code' => 'TEST-005', 'status' => 1]);
    $service = new UnitConversionService();

    expect($service->getChain($sku->id))->toBeEmpty()
        ->and($service->formatHuman($sku->id, 100))->toBe('100');
});

it('validates chain integrity', function () {
    ['sku' => $sku] = createTestSkuWithChain(1, 'TEST-006', [[1, 2, 6], [2, 3, 10]]);
    $service = new UnitConversionService();

    $result = $service->validateChain($sku->id);
    expect($result['valid'])->toBeTrue()
        ->and($result['error'])->toBeNull();
});

it('formats display with conversion note', function () {
    ['sku' => $sku] = createTestSkuWithChain(1, 'TEST-008', [[1, 2, 6], [2, 3, 10]]);
    $service = new UnitConversionService();

    expect($service->formatWithConversion($sku->id, 1, 2))->toBe('2箱（120包）')
        ->and($service->formatWithConversion($sku->id, 2, 5))->toBe('5件（50包）')
        ->and($service->formatWithConversion($sku->id, 3, 7))->toBe('7包');
});

it('handles two level chain', function () {
    ['sku' => $sku] = createTestSkuWithChain(1, 'TEST-009', [[1, 3, 24]]);
    $service = new UnitConversionService();

    expect($service->formatHuman($sku->id, 50))->toBe('2箱2包')
        ->and($service->convertToBase($sku->id, 1, 2))->toBe(48);
});
