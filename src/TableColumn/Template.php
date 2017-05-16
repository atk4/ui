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
    public function __construct($template)
    {
        $this->template = $template;
    }

    public function getDataCellHTML(\atk4\data\Field $f = null)
    {
        return $this->getTag('body', $this->template);
    }
}
