<?php

declare(strict_types=1);

namespace Atk4\Ui\ActionExecutor;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\ActionExecutor\ArgumentForm is deprecated. Use atk4\ui\UserAction\ArgumentFormExecutor instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed in dec-2020
 */
class ArgumentForm extends \atk4\ui\UserAction\ArgumentFormExecutor
{
}
