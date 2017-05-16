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
        $this->template = is_object($template) ? $template : new \atk4\ui\Template($template);
    }

    public function getDataCellHTML(\atk4\data\Field $f = null)
    {
        return $this->getTag('body', '{$c_'.$this->short_name.'}');
    }
    public function getHtmlTags($row, $field)
    {
        $this->table->add($this->template);
        return ['c_'.$this->short_name => $this->template->set($row)->render()];
    }
}
