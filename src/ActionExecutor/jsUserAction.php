<?php

declare(strict_types=1);

namespace atk4\ui\ActionExecutor;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\ActionExecutor\jsUserAction is deprecated. Use atk4\ui\UserAction\JsCallbackExecutor instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed in dec-2020
 */
class jsUserAction extends \atk4\ui\UserAction\JsCallbackExecutor
{
}
