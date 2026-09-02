# Excel 导入导出开发指南

> 适用系统：本地速送服务平台（Laravel 13 + Livewire 4）
> 依赖包：`maatwebsite/excel ^3.1`（composer.json）
> 本手册整理系统中 Excel 导入导出的通用实现，供后续开发快速接入参考。

---

## 目录

1. [两种实现模式](#1-两种实现模式)
2. [核心文件清单](#2-核心文件清单)
3. [列表页模式：trait 接入](#3-列表页模式trait-接入)
4. [详情页模式：自定义实现](#4-详情页模式自定义实现)
5. [GenericExport 详解](#5-genericexport-详解)
6. [GenericImport 详解](#6-genericimport-详解)
7. [金额单位处理](#7-金额单位处理)
8. [新列表页接入步骤清单](#8-新列表页接入步骤清单)

---

## 1. 两种实现模式

| 对比项 | 列表页模式（trait 复用） | 详情页模式（自定义实现） |
|--------|------------------------|------------------------|
| 适用场景 | 标准列表页（含筛选/分页/列配置） | 详情页子表（如单据明细）、非标准结构 |
| 实现方式 | `use WithExcelExport, WithExcelImport` + 覆盖约定方法 | 手写弹窗状态 + `Excel::toCollection()` 逐行处理 |
| 导入解析 | `GenericImport`（表头映射、去重、值转换全自动） | 匿名类解析原始行，业务校验自由发挥 |
| 弹窗视图 | `partials/export-modal` / `partials/import-modal` 直接 include | 视图内自写弹窗（参考 loss-order-detail.blade.php） |
| 典型案例 | `LossOrderList`（损耗单列表） | `LossOrderDetail`（损耗单明细管理） |

选择原则：**能用 trait 就用 trait**；只有当导入目标不是"一个模型一行"（如明细需要关联 SKU 校验、回写单据总额）时才走详情页自定义模式。

---

## 2. 核心文件清单

```
app/
├── Exports/GenericExport.php          # 通用导出类（query / 静态数组双模式）
├── Imports/GenericImport.php          # 通用导入类（表头映射 + 逐行校验收集）
├── Livewire/Traits/
│   ├── WithExcelExport.php            # 列表页导出 trait（含模板下载）
│   ├── WithExcelImport.php            # 列表页导入 trait（含 WithFileUploads）
│   └── WithColumnVisibility.php       # 列配置 trait（列表页配套）
└── helpers.php                        # money_format / money_to_cents 等金额函数

resources/views/partials/
├── export-modal.blade.php             # 导出弹窗（格式选择 + 列勾选）
├── import-modal.blade.php             # 导入弹窗（模板下载 + 错误明细展示）
└── column-modal.blade.php             # 列配置弹窗
```

参考实现：

- 列表页 trait 接入：`app/Livewire/Loss/LossOrderList.php`
- 详情页自定义实现：`app/Livewire/Loss/LossOrderDetail.php`
- 详情页视图（自写导入导出弹窗）：`resources/views/livewire/loss/loss-order-detail.blade.php`

---

## 3. 列表页模式：trait 接入

### 3.1 组件引入 trait

```php
// app/Livewire/Loss/LossOrderList.php
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;

class LossOrderList extends Component
{
    use WithColumnVisibility, WithExcelExport, WithExcelImport;
    // ...
}
```

`WithExcelImport` 内部已 `use WithFileUploads`，无需重复引入。

### 3.2 组件必须覆盖的方法

**导出相关：**

```php
// 所有列定义（列配置弹窗 + 导出列勾选共用）
// type=money 的列会被 getMoneyColumns() 自动识别为金额列（导出 ÷1000 转元）
public function getAllColumns(): array
{
    return [
        ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
        ['key' => 'loss_no', 'label' => '损耗单号', 'sortable' => true, 'exportable' => true],
        ['key' => 'total_amount', 'label' => '损耗金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
        // exportable=false 的列不出现在导出弹窗
    ];
}

// 导出数据源：返回 Eloquent Builder
// 注意：trait 默认导出全量（不带列表页筛选条件），如需"按当前筛选导出"，
// 这里应复用 render() 中相同的筛选逻辑构建 query
public function getExportQuery()
{
    return LossOrder::with(['warehouse'])->orderBy('id', 'desc');
}

// 导出文件名（不含扩展名）
public function getExportFileName(): string
{
    return '损耗管理_'.now()->format('Ymd_His');
}
```

**导入相关：**

```php
// 导入目标模型
public function getImportModelClass(): string
{
    return LossOrder::class;
}

// Excel 中文表头 → 模型字段映射
// 模板表头即 array_keys()，保证模板与导入映射天然一致
public function getImportColumnMap(): array
{
    return [
        '损耗单号' => 'loss_no',
        '仓库ID' => 'warehouse_id',
        '损耗类型' => 'loss_type',
        '原因' => 'reason',
    ];
}

// 唯一键字段（命中即跳过，计数到 skipped）
public function getImportUniqueBy(): array
{
    return ['loss_no'];
}

// 必填列中文名（导入弹窗提示 + 模板表头标红）
public function getImportRequiredFields(): array
{
    return ['仓库ID', '损耗类型'];
}

// 值转换映射（如 Excel 里填"启用"，落库转 1）
public function getImportValueMap(): array
{
    return [];
    // 例：['status' => ['启用' => 1, '禁用' => 2]]
}

// 元→厘转换的金额字段（模型字段名，非中文表头）
public function getImportMoneyFields(): array
{
    return [];
    // 例：['amount', 'unit_price']
}
```

### 3.3 可选：行回调（导出格式化）

默认导出按 `columns` 顺序 `data_get()` 取值。需要关联名称、状态翻译、金额格式化时覆盖：

```php
public function getExportRowCallback(): callable
{
    return function ($row) {
        return [
            'id' => $row->id,
            'loss_no' => $row->loss_no,
            'warehouse_id' => $row->warehouse?->name ?? '',   // 外键列导出关联名称
            'total_amount' => money_format($row->total_amount, false), // 金额格式化
            'status' => $row->status,
            'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
        ];
    };
}
```

回调返回关联数组时，`GenericExport::map()` 会按导出列顺序自动过滤取值（`array_intersect_key`），key 不在导出列内会被丢弃，列序不受影响。

### 3.4 视图接入

列表页视图底部 include 三个弹窗，工具栏加触发按钮：

```blade
{{-- resources/views/livewire/loss/loss-order-list.blade.php --}}

{{-- 工具栏按钮 --}}
<button wire:click="openImportModal" class="...">
    <x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入
</button>
<button wire:click="openExportModal" class="...">
    <x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出
</button>

{{-- 视图末尾 --}}
@include('partials.column-modal')
@include('partials.export-modal')
@include('partials.import-modal')
```

导入弹窗自带：必填列红色提示、唯一验证说明、模板下载按钮、失败行明细（第 N 行 + 原因）列表。

---

## 4. 详情页模式：自定义实现

适用：导入目标不是"一个模型一行"，需要跨表校验（如 SKU 编码查库）或回写父单据（如单据总额）。以 `LossOrderDetail`（损耗单明细）为例。

### 4.1 为什么不用 trait

- trait 的 `GenericImport` 直接 `Model::create()`，无法挂业务校验（SKU 是否存在）和联动更新（单据 total_amount 累加）；
- 详情页没有 `getAllColumns()` 列配置体系，列定义是独立的 `getItemExportColumns()`；
- 导入导出按钮受 `canEdit()` 状态控制（单据锁定后禁用），trait 无此钩子。

### 4.2 组件骨架

```php
class LossOrderDetail extends Component
{
    use WithToast;
    use WithFileUploads;   // 手动引入（不再经由 WithExcelImport）

    // 明细导入
    public bool $showImportModal = false;
    public $importFile = null;
    public string $importMessage = '';
    public array $importErrors = [];

    // 明细导出
    public bool $showExportModal = false;
    public array $exportColumns = [];
    public string $exportFormat = 'xlsx';

    // 列定义：key => 中文标签（区别于列表页的结构化数组）
    public function getItemExportColumns(): array
    {
        return [
            'sku_code' => 'SKU编码',
            'sku_name' => '商品名称',
            'quantity' => '数量',
            'cost_price' => '成本价',
            'loss_amount' => '损耗金额',
            'responsible_party' => '责任方',
            'supplier' => '供应商',
            'reason' => '原因',
        ];
    }

    public function getImportColumnMap(): array
    {
        return [
            'SKU编码' => 'sku_code',
            '数量' => 'quantity',
            '成本价' => 'cost_price',
            '责任方' => 'responsible_party',
            '供应商' => 'supplier',
            '原因' => 'reason',
        ];
    }

    public function getImportRequiredFields(): array
    {
        return ['SKU编码', '数量', '成本价'];
    }
}
```

### 4.3 导出：静态集合 + 行回调

```php
public function doExport()
{
    $export = new GenericExport(
        queryOrData: $this->lossOrder->items()->with(['sku.product', 'supplier'])->get(), // Collection 也行
        columns: $this->exportColumns,
        columnLabels: collect($this->getItemExportColumns())
            ->filter(fn($label, $key) => in_array($key, $this->exportColumns))->toArray(),
        rowCallback: function ($item) {
            return [
                'sku_code' => $item->sku?->sku_code ?? '',
                'sku_name' => $item->sku?->product?->name ?? '',
                'cost_price' => money_format($item->cost_price, false),
                'loss_amount' => money_format($item->loss_amount, false),
                'responsible_party' => $item->responsible_party == 2 ? '供应商' : '平台',
                // ...
            ];
        },
    );

    return Excel::download($export, "损耗明细_{$this->lossOrder->loss_no}.{$this->exportFormat}");
}
```

注意：传入的是 Collection 而非 Builder（`GenericExport::collection()` 里 `$this->query->get()`，Collection 同样有 `->get()`? 不——Collection 没有 get()，详见 5.2 说明）。

### 4.4 导入：匿名类 + 逐行校验

```php
public function doImport(): void
{
    $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

    // 1. 用匿名 ToCollection 把整个文件读成原始行集合（不带 WithHeadingRow）
    $rows = Excel::toCollection(new class implements \Maatwebsite\Excel\Concerns\ToCollection {
        public function collection(\Illuminate\Support\Collection $rows)
        {
            return $rows;   // 透传：$rows 是「每行一个 SheetCollection」的结构
        }
    }, $this->importFile);

    // 2. 预加载关联数据（避免逐行查库）
    $skus = Sku::with('product')->get()->keyBy('sku_code');

    // 3. 第一行是表头，建立 列下标 → 字段 映射
    $headerRow = $rows->first();
    $headers = $headerRow->first()->toArray();  // 第一个 sheet 的第一行
    $dataRows = $rows->slice(1);                // 其余为数据行

    // 4. 逐行：映射 → 校验 → 创建 → 回写父单据
    foreach ($dataRows as $index => $row) {
        $rowData = [];
        foreach ($headers as $colIndex => $headerName) {
            $field = $columnMap[$headerName] ?? null;
            if ($field && isset($row[$colIndex])) {
                $rowData[$field] = $row[$colIndex];
            }
        }

        // 校验失败 → 收集错误继续下一行（不中断整体导入）
        if (empty($rowData['sku_code'])) {
            $errors[] = ['row' => $index + 2, 'error' => 'SKU编码不能为空'];
            continue;
        }
        $sku = $skus->get($rowData['sku_code']);
        if (!$sku) {
            $errors[] = ['row' => $index + 2, 'error' => "SKU编码 {$rowData['sku_code']} 不存在"];
            continue;
        }

        // 金额元→厘转换 + 小计
        $costPriceCents = (int) round((float) $rowData['cost_price'] * 1000);
        $lossAmount = $costPriceCents * (int) $rowData['quantity'];

        LossOrderItem::create([
            'loss_order_id' => $this->lossOrder->id,
            'sku_id' => $sku->id,
            'loss_type' => $this->lossOrder->loss_type,
            'quantity' => (int) $rowData['quantity'],
            'cost_price' => $costPriceCents,
            'loss_amount' => $lossAmount,
            // ...
        ]);

        // 回写父单据总额
        $this->lossOrder->update([
            'total_amount' => $this->lossOrder->total_amount + $lossAmount,
        ]);
    }

    // 5. 汇总提示：成功 N 条 / 失败 M 条，失败明细展示在弹窗内
}
```

关键设计点：

- **错误行不中断**：单行失败记入 `$errors[]`（行号从 2 起，跳过表头），全部处理完统一汇报；
- **预加载关联**：`Sku::get()->keyBy('sku_code')` 一次查全，导入 100 行只查 1 次库；
- **行号计算**：`$index + 2`（数据行索引 + 表头行 + Excel 行从 1 数）；
- **父单据回写**：每创建一条明细同步累加 `total_amount`，与手动添加明细逻辑保持一致。

### 4.5 视图按钮状态控制

```blade
{{-- 明细区工具栏：仅 canEdit() 时显示导入导出按钮 --}}
@if($canEditItems)
    <button wire:click="openImportModal">导入明细</button>
    <button wire:click="openExportModal">导出明细</button>
@endif
```

`canEditItems = $this->lossOrder->canEdit()` 在 render() 中计算传入视图。单据审核/执行后锁定，明细不可再变更。

---

## 5. GenericExport 详解

`app/Exports/GenericExport.php`

### 5.1 构造参数

```php
new GenericExport(
    $queryOrData,        // Builder（查询导出）| array（静态数据/模板）
    array $columns,      // 导出列 key 列表；数组模式下是表头文字列表
    array $columnLabels, // key => 中文标签
    ?callable $rowCallback = null,
    array $moneyColumns = [],      // 金额列 key（导出 ÷1000 转元）
    array $requiredHeadings = [],  // 模板必填表头（标红）
)
```

### 5.2 三种输入模式

构造函数按 `$queryOrData` 类型分流，三种模式互不影响：

| 模式 | 输入类型 | 表头来源 | 典型用途 |
|------|---------|---------|---------|
| 静态数组 | `array` | `columns` 直接作为表头 | 导入模板下载（空数据 + 中文表头 + 必填标红） |
| Builder | `Eloquent\Builder` | `columnLabels` 按 `columns` 顺序映射 | 列表页全量/筛选导出（支持 chunk 分批读取） |
| Collection | `Illuminate\Support\Collection` | 同 Builder 模式 | 详情页子表导出（数据已查好，如 `$order->items()->get()`） |

Collection 模式与 Builder 模式共用 `columnLabels` / `rowCallback` / `moneyColumns` / `requiredHeadings` 逻辑：

```php
// LossOrderDetail::doExport() —— Collection 模式
$export = new GenericExport(
    queryOrData: $this->lossOrder->items()->with(['sku.product', 'supplier'])->get(),
    columns: $this->exportColumns,
    columnLabels: $columnLabels,
    rowCallback: fn ($item) => [...],  // 与 Builder 模式相同的行回调机制
);
```

**易错点**：Collection 与 Builder 外观相似但类型不同。直接传 `->get()` 的结果是 Collection，走 Collection 分支；传 `->items()` / `->with(...)` 这种未执行查询的 Builder 走 Builder 分支（导出时才真正查库）。两种写法都能工作，但 Collection 模式会在调用时就执行查询、内存中持有全部数据，大表建议用 Builder 模式（chunkSize 500 分批）。

### 5.3 金额列处理

```php
// map() 中：
if (is_numeric($value) && in_array($col, $this->moneyColumns)) {
    $value = round($value / 1000, 2);   // 厘 → 元
}
```

金额列**显式声明**，不用正则猜测列名（历史方案已废弃，避免误命中）。列表页 trait 的 `getMoneyColumns()` 自动从 `getAllColumns()` 中筛 `type=money` 的列。

### 5.4 必填表头标红

`styles()` 对 `requiredHeadings` 命中的表头单元格设置红色加粗 + 淡红背景（`DC2626` / `FEE2E2`），用于导入模板，用户一眼识别必填列。

---

## 6. GenericImport 详解

`app/Imports/GenericImport.php`

### 6.1 构造参数

```php
new GenericImport(
    string $modelClass,  // 目标模型
    array $columnMap,    // 中文表头 → 字段
    array $uniqueBy = [],    // 唯一键字段（命中跳过）
    array $valueMap = [],    // 字段 => [显示值 => 存储值]
    array $moneyFields = [], // 元→厘字段
)
```

### 6.2 处理流程（collection 内逐行）

1. **取值映射**：`WithHeadingRow` 读中文表头为 key，`columnMap` 转字段名；兼容大小写（`$row[strtolower($excelKey)]`）
2. **值转换**：`valueMap` 命中则转换（如 `'启用' => 1`），未命中原样保留
3. **金额转换**：`moneyFields` 中的字段经 `money_to_cents()` 元→厘
4. **空行跳过**：映射后 `$data` 为空 → skipped++
5. **唯一性检查**：`uniqueBy` 字段组合查库，已存在 → skipped++
6. **创建**：`Model::create($data)` → imported++
7. **异常收集**：单行异常记入 `errorRows`（`['row' => 行号, 'error' => 消息]`），不中断

### 6.3 结果统计

```php
$import->getImportedCount();  // 成功数
$import->getSkippedCount();   // 跳过数（空行 + 重复）
$import->getErrorRows();      // [['row' => 2, 'error' => 'xxx'], ...]
```

trait 的 `doImport()` 按结果分三档 toast：有错误 → error；仅跳过 → warning；全成功 → success。

### 6.4 局限（何时该换详情页模式）

- 只能整行直接 `create()`，**不支持跨表校验**（外键是否存在）、**不支持回写父单据**；
- 无事务包裹，导入一半失败时已创建的记录不回滚（单行失败跳过、继续后续行，属于"尽力导入"策略）。

---

## 7. 金额单位处理

系统金额体系：**数据库存厘（bigint），展示/导入导出用元**。

```
存储层（厘）→ 计算层（厘）→ 显示层（元）
```

### 7.1 辅助函数（app/helpers.php）

| 函数 | 方向 | 说明 |
|------|------|------|
| `money_format($cents, $withSymbol, $module)` | 厘 → 元字符串 | 支持模块级舍入配置；`money_format(8000, false)` → `8.00` |
| `money_round($cents, $precision, $mode)` | 厘 → 元浮点 | 四种舍入：round / round_up / round_down / truncate |
| `money_to_cents($yuan)` | 元 → 厘 | `'8.00'` → 8000，导入必经 |

### 7.2 导入导出中的对应配置

| 场景 | 配置项 | 位置 |
|------|--------|------|
| 列表页导出（无行回调） | `getAllColumns()` 中列加 `'type' => 'money'` | trait 自动收集 `getMoneyColumns()` |
| 列表页导出（有行回调） | 回调内手动 `money_format($row->xxx, false)` | 回调结果不再走 moneyColumns 逻辑 |
| 详情页导出 | 行回调内手动 `money_format()` | 同上 |
| 列表页导入 | `getImportMoneyFields()` 返回字段名数组 | GenericImport 调 `money_to_cents()` |
| 详情页导入 | 代码中手动 `(int) round($yuan * 1000)` 或 `money_to_cents()` | 自由控制 |
**注意**：行回调与 moneyColumns 互斥——回调返回值直接输出，不再应用金额转换。用了回调的导出，金额格式化要在回调里自己做。

---

## 8. 新列表页接入步骤清单

给新列表页加导入导出，按序执行：

1. **引入 trait**：`use WithColumnVisibility, WithExcelExport, WithExcelImport;`（前两个通常已有）
2. **定义列**：`getAllColumns()` 中每个列加 `exportable`（默认 true），金额列加 `type => money`
3. **导出三件套**：覆盖 `getExportQuery()` / `getExportFileName()`，需要格式化再覆盖 `getExportRowCallback()`
4. **导入四件套**：覆盖 `getImportModelClass()` / `getImportColumnMap()` / `getImportUniqueBy()` / `getImportRequiredFields()`；有值转换加 `getImportValueMap()`，有金额加 `getImportMoneyFields()`
5. **视图三行**：工具栏加导入/导出按钮（`wire:click="openImportModal"` / `openExportModal`），视图末尾 include 三个 partials
6. **验证**：
   - 导出：xlsx/csv 两种格式、勾选部分列、金额列单位正确
   - 模板下载：表头与 columnMap 一致、必填列标红
   - 导入：正常数据、缺必填、重复数据（uniqueBy 跳过）、金额元→厘落库正确
7. **收尾**：`php artisan view:clear` 清缓存，按 Git 规范提交

---

## 附：常见问题

**Q：导出想带列表页当前筛选条件怎么办？**
A：`getExportQuery()` 里复用 render() 的筛选逻辑（把筛选条件抽成私有方法 `buildFilteredQuery()` 两处共用）。当前 `LossOrderList` 导出全量数据。

**Q：CSV 中文乱码？**
A：maatwebsite/excel 3.1 的 CSV 写出默认 UTF-8 无 BOM，Excel 直接打开可能乱码。如需兼容，导出 csv 时在 `Excel::download()` 前对内容加 BOM（系统当前未特殊处理，xlsx 无此问题）。

**Q：导入的行号为什么从 2 开始？**
A：第 1 行是表头，数据从第 2 行起；错误提示 `第 N 行` 与用户在 Excel 里看到的行号一致（WithHeadingRow 模式下 maatwebsite 已跳过表头，`$index` 从 0 计数据行，故 `$index + 2`）。

**Q：为什么不用正则自动识别金额列？**
A：历史方案按列名含 amount/price 猜测，存在误命中（如 `price_note`）和除数错误（有列存元有列存厘）。现改为显式声明 `moneyColumns`，配合列配置的 `type=money` 统一来源。