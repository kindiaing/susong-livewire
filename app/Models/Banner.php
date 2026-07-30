<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 轮播广告模型
 *
 * @property mixed $title 标题
 * @property mixed $image_url 图片地址
 * @property mixed $link_url 跳转链接
 * @property mixed $sort 排序
 * @property mixed $status 状态：0禁用1启用
 */
class Banner extends Model
{

    protected $fillable = [
        'title',
        'image_url',
        'link_url',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'status' => 'integer',
        ];
    }

}
