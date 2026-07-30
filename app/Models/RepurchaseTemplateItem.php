<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 复购模板明细模型
 *
 * @property int $id
 * @property int $template_id 模板ID
 * @property int $sku_id SKU ID
 * @property int $quantity 数量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RepurchaseTemplateItem extends Model
{
    protected $fillable = [
        'template_id',
        'sku_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'template_id' => 'integer',
            'sku_id' => 'integer',
            'quantity' => 'integer',
        ];
    }

    public function template()
    {
        return $this->belongsTo(RepurchaseTemplate::class, 'template_id');
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
}
