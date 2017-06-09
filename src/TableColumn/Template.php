<?php

namespace atk4\ui\TableColumn;

/**
 * Implements Column helper for grid.
 */
class Template extends Generic
{
    /**
     * User-defined template for this Column.
     *
     * @var string
     */
    public $template = null;

    /**
     * call new TableColumn\Template('{$name} {$surname}');.
     *
     * @param string $template Template with {$tags}
     */
    public function setDefaults($template = [], $strict = false)
    {
        if (is_array($template) && isset($template[0])) {
            $this->template = $template[0];
        } elseif (is_string($template)) {
            $this->template = $template;
        }
    }

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        return $this->template;
    }
}
