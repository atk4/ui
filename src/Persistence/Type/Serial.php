<?php

declare(strict_types=1);

namespace atk4\ui\Persistence\Type;

use atk4\data\Field;

/**
 * Handle Array and Object type cast.
 */
class Serial extends Basic implements Castable
{
    public static function castLoadValue(Field $field, $value)
    {
        return $value;
    }

    public static function castSaveValue(Field $field, $value)
    {
        return $field->serialize ? $value : json_encode($value, JSON_THROW_ON_ERROR);
    }
}
