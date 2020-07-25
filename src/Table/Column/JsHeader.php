<?php

declare(strict_types=1);

namespace atk4\ui\Table\Column;

use atk4\ui\JsCallback;

/**
 * Implement a callback for a column header dropdown menu.
 */
class JsHeader extends JsCallback
{
    /**
     * Function to call when header menu item is select.
     *
     * @param callable $fx
     */
    public function onSelectItem($fx)
    {
        if (is_callable($fx)) {
            $param = [$_GET['id'] ?? null,  $_GET['item'] ?? null];
            $this->set(function () use ($fx, $param) {
                return call_user_func_array($fx, $param);
            });
        }
    }
}
