<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
interface JsExpressionable
{
    public function jsRender(): string;
}
