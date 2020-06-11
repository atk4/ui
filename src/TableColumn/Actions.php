<?php

namespace atk4\ui\TableColumn;

use atk4\data\Field;
use atk4\data\Model;
use atk4\ui\Button;

/**
 * Formatting action buttons column.
 */
class Actions extends Generic
{
    /**
     * Stores all the buttons that have been added.
     *
     * @var array
     */
    public $actions = [];

    /**
     * Callbacks as defined in $isDisabled for evaluating row-specific if an action is enabled.
     *
     * @var array
     */
    protected $callbacks = [];

    public function init()
    {
        parent::init();
        $this->addClass('right aligned');
    }

    /**
     * Adds a new button which will execute $callback when clicked.
     *
     * @param bool|callable $isDisabled Should button be disabled?
     *
     * @return Button
     */
    public function addAction($button, $callback, $confirm = false, $isDisabled = false)
    {
        $name = $this->name.'_action_'.(count($this->actions) + 1);

        if (!is_object($button)) {
            $button = new Button($button);
        }
        $button->app = $this->table->app;

        $this->actions[$name] = $button->addClass('{$_' . $name . '_disabled} compact b_' . $name);

        if ($isDisabled === true) {
            $button->addClass('disabled');
        } elseif (is_callable($isDisabled)) {
            $this->callbacks[$name] = $isDisabled;
        }

        $this->table->on('click', '.b_'.$name, $callback, [$this->table->jsRow()->data('id'), 'confirm' => $confirm]);

        return $button;
    }

    /**
     * Adds a new button which will open a modal dialog and dynamically
     * load contents through $callback. Will pass a virtual page.
     */
    public function addModal($button, $title, $callback, $owner = null)
    {
        if (!$owner) {
            $modal = $this->owner->owner->add(['Modal', 'title'=>$title]);
        } else {
            $modal = $owner->add(['Modal', 'title'=>$title]);
        }
        $modal->observeChanges(); // adds scrollbar if needed

        $modal->set(function ($t) use ($callback) {
            call_user_func($callback, $t, $this->app->stickyGet($this->name));
        });

        return $this->addAction($button, $modal->show([$this->name=>$this->owner->jsRow()->data('id')]));
    }

    /**
     * {@inheritdoc}
     */
    public function getTag($position, $value, $attr = [])
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $value, $attr);
    }

    public function getDataCellTemplate(Field $f = null)
    {
        if (!$this->actions) {
            return '';
        }

        // render our actions
        $output = '';
        foreach ($this->actions as $action) {
            $output .= $action->getHTML();
        }

        return '<div class="ui buttons">'.$output.'</div>';
    }

    public function getHTMLTags(Model $row, $field)
    {
        $tags = [];
        foreach ($this->callbacks as $name => $callback) {
            // if action is disabled then set disabled class
            if (call_user_func($callback, $row)) {
                $tags['_' . $name . '_disabled'] = 'disabled';
            }
        }

        return $tags;
    }

    // rest will be implemented for crud
}
