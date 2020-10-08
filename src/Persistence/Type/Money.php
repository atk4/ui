<?php

declare(strict_types=1);

namespace atk4\ui\Persistence\Type;

use atk4\data\Field;

class Money extends Basic implements Castable
{
    public static $props = [
        'currency' => '€',
        'decimal' => 2,
    ];

    public static function castLoadValue(Field $field, $value)
    {
        return str_replace(',', '', $value);
    }

    public static function castSaveValue(Field $field, $value)
    {
        return (static::getProps('currency') ? static::getProps('currency') . ' ' : '') . number_format($value, static::getProps('decimal'));
    }
}
