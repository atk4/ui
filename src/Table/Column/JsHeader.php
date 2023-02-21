<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\JsCallback;
use Atk4\Ui\View;

/**
 * Implement a callback for a column header dropdown menu.
 */
class JsHeader extends JsCallback
{
    /**
     * Function to call when header menu item is select.
     *
     * @param \Closure(string|null, string|null): (JsExpressionable|View|string|void) $fx
     */
    public function onSelectItem(\Closure $fx): void
    {
        $this->set(function () use ($fx) {
            return $fx($_GET['id'] ?? null, $_GET['item'] ?? null);
        });
    }
}
