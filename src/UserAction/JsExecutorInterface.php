<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

/**
 * Add js trigger for executing an action.
 */
interface JsExecutorInterface extends ExecutorInterface
{
    /**
     * Return js expression that will trigger action executor.
     */
    public function jsExecute(array $urlArgs);
}
