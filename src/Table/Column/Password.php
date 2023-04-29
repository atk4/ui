<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Ui\Table;

/**
 * Implements Column helper for grid.
 */
class Password extends Table\Column
{
    public $sortable = false;

    public function getDataCellTemplate(Field $field = null): string
    {
        return '***';
    }
}
