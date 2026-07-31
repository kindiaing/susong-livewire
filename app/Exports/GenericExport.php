<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * 通用导出类
 * 接受查询构建器、列定义、列标签，生成 Excel 文件
 *
 * 金额列处理：通过 $moneyColumns 显式声明（厘→元 ÷1000），
 * 不再用正则猜测列名，避免误命中和除数错误。
 */
class GenericExport implements FromCollection, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping
{
    protected ?Builder $query;

    protected array $columns;

    protected array $columnLabels;

    protected $rowCallback;

    protected ?array $staticData;

    protected ?array $staticHeadings;

    /**
     * 金额列 key 列表（存储单位厘，导出时 ÷1000 转为元）
     */
    protected array $moneyColumns;

    public function __construct(
        $queryOrData = null,
        array $columns = [],
        array $columnLabels = [],
        ?callable $rowCallback = null,
        array $moneyColumns = [],
    ) {
        // 支持纯数组数据（用于非模型导出 / 导入模板）
        if (is_array($queryOrData)) {
            $this->staticData = $queryOrData;
            $this->staticHeadings = $columns;
            $this->query = null;
            $this->columns = [];
            $this->columnLabels = [];
            $this->rowCallback = null;
            $this->moneyColumns = [];
        } else {
            $this->query = $queryOrData;
            $this->columns = $columns;
            $this->columnLabels = $columnLabels;
            $this->rowCallback = $rowCallback;
            $this->staticData = null;
            $this->staticHeadings = null;
            $this->moneyColumns = $moneyColumns;
        }
    }

    public function collection()
    {
        if ($this->staticData !== null) {
            return collect($this->staticData);
        }
        if (! $this->query) {
            return collect([]);
        }

        return $this->query->get();
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        if ($this->staticHeadings !== null) {
            return $this->staticHeadings;
        }

        return array_map(fn ($key) => $this->columnLabels[$key] ?? $key, $this->columns);
    }

    public function map($row): array
    {
        // 纯数组模式：直接返回行数据
        if ($this->staticData !== null) {
            return is_array($row) ? array_values($row) : [];
        }

        if ($this->rowCallback) {
            $result = call_user_func($this->rowCallback, $row, $this->columns);

            // 如果回调返回关联数组，按导出列顺序过滤取值
            if (is_array($result) && array_keys($result) !== range(0, count($result) - 1)) {
                return array_values(array_intersect_key(
                    array_merge(array_fill_keys($this->columns, ''), $result),
                    array_flip($this->columns)
                ));
            }

            return $result;
        }

        $result = [];
        foreach ($this->columns as $col) {
            $value = data_get($row, $col);

            // 关联字段：用 . 分隔取值（如 merchant.name）
            if (str_contains($col, '.')) {
                $value = data_get($row, $col);
            }

            // 金额列格式化：厘 → 元（÷1000），仅对显式声明的列处理
            if (is_numeric($value) && in_array($col, $this->moneyColumns)) {
                $value = round($value / 1000, 2);
            }

            $result[] = $value ?? '';
        }

        return $result;
    }
}
