<?php

declare(strict_types=1);

namespace Atk4\Data;

use Atk4\Data\Persistence\Sql\Expressionable;

/**
 * @property Persistence\Sql\Join $join
 */
class FieldSql extends Field implements Expressionable
{
    /**
     * SQL fields are allowed to have expressions inside of them.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalize($value)
    {
        if ($value instanceof Expressionable) {
            return $value;
        }

        return parent::normalize($value);
    }
}
