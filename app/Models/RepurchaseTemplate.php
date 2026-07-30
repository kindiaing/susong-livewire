<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 复购模板模型
 *
 * @property int $id
 * @property int $merchant_id 商家ID
 * @property string $name 模板名称
 * @property int $status 状态
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RepurchaseTemplate extends Model
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'merchant_id',
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items()
    {
        return $this->hasMany(RepurchaseTemplateItem::class, 'template_id');
    }
}
