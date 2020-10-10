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

    public function castSaveValue(Field $field, $value)
    {
        return $value ? static::getProps('yes') : static::getProps('no');
    }

    public function castLoadValue(Field $field, $value)
    {
        return (bool) $value;
    }
}
