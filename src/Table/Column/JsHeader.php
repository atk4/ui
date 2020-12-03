<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Ui\JsCallback;

/**
 * Implement a callback for a column header dropdown menu.
 */
class JsHeader extends JsCallback
{
    /**
     * Function to call when header menu item is select.
     */
    public function onSelectItem(\Closure $fx)
    {
        $this->set(function () use ($fx) {
            return $fx($_GET['id'] ?? null, $_GET['item'] ?? null);
        });
    }
}
