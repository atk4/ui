<?php

namespace atk4\ui\TableColumn;

/**
 * Implement drag handler column for sorting table.
 */
class DragHandler extends Generic
{
    public $class = null;
    public $tag = 'i';
    public $cb = null;

    public function init()
    {
        parent::init();

        if (!$this->class) {
            $this->class = 'content icon';
        }
        $this->cb = $this->table->add(['jsSortable', 'handleClass' => 'atk-handle']);
    }

    /**
     * Callback when table has been reorder using handle.
     *
     * @param null $fx
     */
    public function onReorder($fx = null)
    {
        $this->cb->onReorder($fx);
    }

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        return $this->app->getTag($this->tag, ['class' => $this->class.' atk-handle', 'style'=>'cursor:pointer; color: #bcbdbd']);
    }
}
