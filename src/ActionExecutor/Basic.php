<?php

declare(strict_types=1);

namespace atk4\ui\ActionExecutor;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\ActionExecutor\Basic is deprecated. Use atk4\ui\UserAction\BasicExecutor instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed in dec-2020
 */
class Basic extends \atk4\ui\UserAction\BasicExecutor
{
}
