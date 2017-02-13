<?php

namespace atk4\ui\Column;

/**
 * Implements Column helper for grid.
 */
class Template extends Generic
{
    public $template = null;

    function __construct($template) {
        $this->template = $template;
    }

    public function getCellTemplate(\atk4\data\Field $f)
    {
        return $this->template;
    }
}
