<?php

namespace App\Support;

use App\Models\SystemConfig;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static mixed group(string $group)
 * @method static void reset(string $key)
 * @method static void flush()
 *
 * @see \App\Models\SystemConfig
 */
class Setting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'setting';
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return SystemConfig::getValue($key, $default);
    }

    public static function set(string $key, mixed $value): void
    {
        SystemConfig::setValue($key, $value);
    }

    public static function group(string $group): \Illuminate\Support\Collection
    {
        return SystemConfig::getGroup($group);
    }

    public static function reset(string $key): void
    {
        SystemConfig::resetToDefault($key);
    }

    public static function flush(): void
    {
        SystemConfig::flushCache();
    }
}
