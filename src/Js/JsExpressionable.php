<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

/**
 * Allow to map class into JavaScript expression.
 */
interface JsExpressionable
{
    public function jsRender(): string;
}
