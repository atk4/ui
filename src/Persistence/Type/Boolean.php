<?php

declare(strict_types=1);

namespace atk4\ui\Persistence\Type;

use atk4\data\Field;

class Boolean extends Basic implements Castable
{
    public static $props = [
        'yes' => 'Yes',
        'no' => 'No',
    ];

    public static function castSaveValue(Field $field, $value)
    {
        return $value ? static::getProps('yes') : static::getProps('no');
    }

    public static function castLoadValue(Field $field, $value)
    {
        return (bool) $value;
    }
}
