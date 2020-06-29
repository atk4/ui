<?php

declare(strict_types=1);

namespace atk4\ui\UserAction;

/**
 * Add js trigger for executing an action.
 */
interface JsExecutorInterface extends ExecutorInterface
{
    /**
     * Return js expression that will trigger action executor.
     *
     * @return mixed
     */
    public function jsExecute(array $urlArgs);
}
