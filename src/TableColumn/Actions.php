<?php

namespace atk4\ui\TableColumn;

/**
 * Formatting action buttons column.
 */
class Actions extends Generic
{
    public $actions = [];

    public function init()
    {
        parent::init();
        $this->addClass('right aligned');
    }

    public function addAction($button, $callback, $confirm = false)
    {
        $name = 'action_'.(count($this->actions) + 1);

        if (!is_object($button)) {
            $button = new \atk4\ui\Button($button);
        }
        $button->app = $this->table->app;

        $this->actions[$name] = $button;
        $button->addClass('b_'.$name);
        $button->addClass('compact');
        $this->table->on('click', '.b_'.$name, $callback, [$this->table->jsRow()->data('id'), 'confirm'=>'Are you sure?']);
    }

    public function getDataCellHTML(\atk4\data\Field $f = null)
    {
        $output = '';

        // render our actions
        foreach ($this->actions as $action) {
            $output .= $action->getHTML();
        }

        return $this->getTag('td', 'body', [$output]);
    }

    // rest will be implemented for crud
}
