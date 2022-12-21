<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Ui\JsExpressionable;

/**
 * Add js trigger for executing an action.
 */
interface JsExecutorInterface extends ExecutorInterface
{
    /**
     * Return js expression that will trigger action executor.
     *
     * @return array<int, JsExpressionable>
     */
    public function jsExecute(array $urlArgs): array;
}
