<?php

declare(strict_types=1);
/**
 * Table column action menu.
 * Will create a dropdown menu within table column.
 */

namespace Atk4\Ui\TableColumn;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\TableColumn\ActionMenu is deprecated. Use atk4\ui\Table\Column\ActionMenu instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class ActionMenu extends \atk4\ui\Table\Column\ActionMenu
{
}
