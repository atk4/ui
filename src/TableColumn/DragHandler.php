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
    public $sortOrders = null;

    public function init()
    {
        parent::init();

        if (!$this->class) {
            $this->class = 'content icon';
        }
        $this->cb = $this->table->add('jsCallback');

        $this->app->requireJS('https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.5/lib/draggable.bundle.js');
        $this->table->js(true)->atkJsSortable(['uri' => $this->cb->getJSURL()]);
    }

    /**
     * Callback when table has been reorder using handle.
     *
     * @param null $fx
     */
    public function onReorder($fx = null)
    {
        if (is_callable($fx)) {
            if ($this->cb->triggered()) {
                $this->sortOrders = explode(',', $_POST['order']);
                $this->cb->set(function () use ($fx) {
                    return call_user_func_array($fx, [$this->sortOrders]);
                });
            }
        }
    }

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        return $this->app->getTag($this->tag, ['class' => $this->class.' atk-handle', 'style'=>'cursor:pointer']);
    }
}
