<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Ui\Js\JsBlock;

/**
 * Add JS trigger for executing an action.
 */
interface JsExecutorInterface extends ExecutorInterface
{
    /**
     * Return JS expression that will trigger action executor.
     *
     * @param array<string, string> $urlArgs
     */
    public function jsExecute(array $urlArgs): JsBlock;
}
