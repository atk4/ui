<?php

namespace atk4\ui\TableColumn;

use atk4\ui\jsCallback;

/**
 * Implement a callback for a header dropdown menu.
 */
class jsHeader extends jsCallback
{
    /**
     * Function to call when header menu has change.
     *
     * @param callable $fx
     */
    public function onChangeItem($fx)
    {
        if (is_callable($fx)) {
            if ($this->triggered()) {
                $param = [$_GET['id'],  @$_GET['item']];
                $this->set(function () use ($fx, $param) {
                    return call_user_func_array($fx, $param);
                });
            }
        }
    }
}
