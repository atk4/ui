<?php

declare(strict_types=1);

namespace atk4\ui\Persistence\Type;

/**
 * Base type class.
 */
class Basic
{
    public static $props = [];

    public static function getProps($type)
    {
        return static::$props[$type] ?? null;
    }

    public static function setProps($type, $format)
    {
        static::$props[$type] = $format;
    }
}
