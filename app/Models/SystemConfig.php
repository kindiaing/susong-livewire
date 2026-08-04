<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemConfig extends Model
{
    use HasFactory;

    protected $table = 'system_configs';

    protected $fillable = [
        'config_key',
        'config_value',
        'default_value',
        'config_type',
        'config_group',
        'label',
        'hint',
        'options',
        'validation_rules',
        'sort_order',
        'is_public',
        'is_readonly',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'sort_order' => 'integer',
            'is_public' => 'boolean',
            'is_readonly' => 'boolean',
        ];
    }

    /**
     * 获取类型转换后的值
     */
    public function getTypedValue(): mixed
    {
        return self::castValue($this->config_value, $this->config_type);
    }

    /**
     * 获取类型转换后的默认值
     */
    public function getTypedDefaultValue(): mixed
    {
        return self::castValue($this->default_value, $this->config_type);
    }

    /**
     * 按 config_key 获取配置值（带类型转换）
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $config = static::getAll()->first(fn($c) => $c->config_key === $key);
        if (!$config) return $default;

        return static::castValue($config->config_value, $config->config_type);
    }

    /**
     * 设置配置值（不存在则自动创建）
     */
    public static function setValue(string $key, mixed $value): void
    {
        $config = static::getAll()->first(fn($c) => $c->config_key === $key);

        if ($config && $config->is_readonly) {
            throw new \RuntimeException("配置项 [{$key}] 为只读，不允许修改");
        }

        // 转换为存储格式
        $storeValue = match($config?->config_type ?? 'string') {
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        if ($config) {
            static::where('config_key', $key)->update(['config_value' => $storeValue]);
        } else {
            // key 不存在，自动创建
            static::create([
                'config_key' => $key,
                'config_value' => $storeValue,
                'default_value' => $storeValue,
                'config_type' => 'string',
                'config_group' => 'finance',
                'label' => $key,
                'sort_order' => 0,
                'is_public' => false,
                'is_readonly' => false,
            ]);
        }

        static::flushCache();
    }

    /**
     * 获取某个分组的所有配置
     */
    public static function getGroup(string $group): \Illuminate\Support\Collection
    {
        return static::getAll()->filter(fn($c) => $c->config_group === $group);
    }

    /**
     * 重置某个配置为默认值
     */
    public static function resetToDefault(string $key): void
    {
        $config = static::getAll()->first(fn($c) => $c->config_key === $key);
        if (!$config) return;
        if ($config->is_readonly) return;

        $default = $config->default_value ?? '';
        static::where('config_key', $key)->update(['config_value' => $default]);
        static::flushCache();
    }

    /**
     * 带缓存的全表读取
     */
    protected static function getAll(): \Illuminate\Support\Collection
    {
        try {
            $result = Cache::remember('system_configs_all', 3600, fn() =>
                static::orderBy('config_group')->orderBy('sort_order')->get()
            );

            // 防御反序列化失败（Migration 变更后旧缓存会返回 __PHP_Incomplete_Class）
            if (!($result instanceof \Illuminate\Support\Collection)) {
                Cache::forget('system_configs_all');
                $result = static::orderBy('config_group')->orderBy('sort_order')->get();
            }

            return $result;
        } catch (\Throwable) {
            Cache::forget('system_configs_all');
            return static::orderBy('config_group')->orderBy('sort_order')->get();
        }
    }

    /**
     * 清除缓存
     */
    public static function flushCache(): void
    {
        Cache::forget('system_configs_all');
    }

    /**
     * 类型转换
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) return null;

        return match($type) {
            'boolean' => in_array($value, ['1', 'true', 'yes'], true),
            'integer' => (int) $value,
            'decimal' => (float) $value,
            default => $value,
        };
    }

    /**
     * 配置分组中文名映射
     */
    public static function groupLabels(): array
    {
        return [
            'basic' => '基础设置',
            'order' => '订单设置',
            'delivery' => '配送设置',
            'finance' => '财务风控',
            'money' => '金额精度',
            'inventory' => '库存设置',
            'audit' => '审核日志',
            'ui' => '界面设置',
        ];
    }

    /**
     * 获取分组标签（Eloquent accessor）
     */
    public function getGroupLabelAttribute(): string
    {
        return static::groupLabels()[$this->config_group] ?? $this->config_group;
    }

    /**
     * 获取分组标签（兼容旧调用）
     */
    public function getGroupLabel(): string
    {
        return static::groupLabels()[$this->config_group] ?? $this->config_group;
    }
}
