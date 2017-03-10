<?php

namespace atk4\ui\TableColumn;

/**
 * Formatting money.
 */
class Actions extends Generic
{
    public $actions = [];

    public function addAction($icon, $callback)
    {
        $cb = $this->add(new jsCallback(), $action);

        $cb->set($callback);
    }

    // rest will be implemented for crud
}
