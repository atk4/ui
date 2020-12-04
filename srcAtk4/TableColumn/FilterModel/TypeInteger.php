<?php

declare(strict_types=1);

namespace Atk4\Ui\TableColumn\FilterModel;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\TableColumn\FilterModel\TypeInteger is deprecated. Use atk4\ui\Table\Column\FilterModel\TypeInteger instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class TypeInteger extends \atk4\ui\Table\Column\FilterModel\TypeInteger
{
}
