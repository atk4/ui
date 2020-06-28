<?php

declare(strict_types=1);

namespace atk4\ui\ActionExecutor;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\ActionExecutor\Interface_ is deprecated. Use atk4\ui\UserAction\ExecutorInterface instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed in dec-2020
 */
interface Interface_ extends \atk4\ui\UserAction\ExecutorInterface
{
}
