<?php

declare(strict_types=1);

namespace atk4\ui;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
interface JsExpressionable
{
    public function jsRender(): string;
}
