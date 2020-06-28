<?php

declare(strict_types=1);

namespace atk4\ui\TableColumn;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\TableColumn\ActionButtons is deprecated. Use atk4\ui\Table\Column\ActionButtons instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class ActionButtons extends \atk4\ui\Table\Column\ActionButtons
{
}
