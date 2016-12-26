<?php

namespace atk4\ui;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
interface jsExpressionable {

    /**
     * Convert jsExpression into string
     */
    function jsRender();
}
