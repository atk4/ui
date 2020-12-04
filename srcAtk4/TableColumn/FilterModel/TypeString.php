<?php

declare(strict_types=1);

namespace Atk4\Ui\TableColumn\FilterModel;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\TableColumn\FilterModel\TypeString is deprecated. Use atk4\ui\Table\Column\FilterModel\TypeString instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class TypeString extends \atk4\ui\Table\Column\FilterModel\TypeString
{
}
