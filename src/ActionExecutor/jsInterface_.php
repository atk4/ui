<?php

/**
 * Add js trigger for executing an action.
 */

namespace atk4\ui\ActionExecutor;

interface jsInterface_
{
    /**
     * Return js expression that will trigger action executor.
     *
     * @param array $urlArgs
     *
     * @return mixed
     */
    public function jsExecute(array $urlArgs);
}
