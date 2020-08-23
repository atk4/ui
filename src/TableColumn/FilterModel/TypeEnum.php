<?php

declare(strict_types=1);

namespace atk4\ui\TableColumn\FilterModel;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\TableColumn\FilterModel\TypeEnum is deprecated. Use atk4\ui\Table\Column\FilterModel\TypeEnum instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class TypeEnum extends \atk4\ui\Table\Column\FilterModel\TypeEnum
{
}
