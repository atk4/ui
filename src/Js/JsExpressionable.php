<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
interface JsExpressionable
{
    /**
     * Converts this arbitrary JavaScript expression into string.
     */
    public function jsRender(): string;
}
