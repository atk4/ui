<?php

declare(strict_types=1);

namespace atk4\ui\Persistence\Type;

use atk4\data\Field;

interface Castable
{
    /**
     * Cast value when loaded from POST request.
     */
    public function castLoadValue(Field $field, $value);

    /**
     * Cast value from database in order to be display in UI.
     */
    public function castSaveValue(Field $field, $value);
}
