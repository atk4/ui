<?php

declare(strict_types=1);

namespace atk4\ui\Table\Column;

use atk4\ui\Table;

/**
 * Implements Column helper for grid.
 */
class Password extends Table\Column
{
    public $sortable = false;

    public function getDataCellTemplate(\Atk4\Data\Field $field = null)
    {
        return '***';
    }
}
