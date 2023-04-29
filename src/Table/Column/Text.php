<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Ui\Table;

/**
 * Implements Column helper for grid.
 */
class Text extends Table\Column
{
    public array $attr = ['all' => ['class' => ['atk-cell-expanded']]];
}
