<?php

namespace atk4\ui\TableColumn;

use atk4\core\FactoryTrait;

/**
 * Formatting action buttons column.
 */
class ActionButtons extends Generic
{
    use FactoryTrait;

    /**
     * Stores all the buttons that have been added.
     *
     * @var array
     */
    public $buttons = [];

    /**
     * Callbacks as defined in $action->enabled for evaluating row-specific if an action is enabled.
     *
     * @var array
     */
    protected $callbacks = [];

    public function init(): void
    {
        parent::init();
        $this->addClass('right aligned');
    }

    /**
     * Adds a new button which will execute $callback when clicked.
     *
     * Returns button object
     *
     * @param \atk4\ui\View|string                        $button
     * @param null|callable|\atk4\data\UserAction\Generic $action
     * @param bool                                        $confirm
     * @param bool                                        $isDisabled
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return \atk4\ui\View
     */
    public function addButton($button, $action = null, $confirm = false, $isDisabled = false)
    {
        // If action is not specified, perhaps it is defined in the model
        if (!$action) {
            if (is_string($button)) {
                $action = $this->table->model->getAction($button);
            } elseif ($button instanceof \atk4\data\UserAction\Generic) {
                $action = $button;
            }

            if ($action) {
                $button = $action->caption;
            }
        }

        $name = $this->name . '_button_' . (count($this->buttons) + 1);

        if ($action instanceof \atk4\data\UserAction\Generic) {
            $button = $action->ui['button'] ?? $button;

            $confirm = $action->ui['confirm'] ?? $confirm;

            $isDisabled = !$action->enabled;

            if (is_callable($action->enabled)) {
                $this->callbacks[$name] = $action->enabled;
            }
        }

        if (!is_object($button)) {
            $button = $this->factory('Button', [$button, 'id' => false], 'atk4\ui');
        }

        if ($button->icon && !is_object($button->icon)) {
            $button->icon = $this->factory('Icon', [$button->icon, 'id' => false], 'atk4\ui');
        }

        $button->app = $this->table->app;

        $this->buttons[$name] = $button->addClass('{$_' . $name . '_disabled} compact b_' . $name);

        if ($isDisabled) {
            $button->addClass('disabled');
        }
        $this->table->on('click', '.b_' . $name, $action, [$this->table->jsRow()->data('id'), 'confirm' => $confirm]);

        return $button;
    }

    /**
     * Adds a new button which will open a modal dialog and dynamically
     * load contents through $callback. Will pass a virtual page.
     *
     * @param \atk4\ui\View|string $button
     * @param string|array         $title - model title or model seed array
     * @param callable             $callback
     * @param \atk4\ui\View        $owner
     * @param array                $args
     *
     * @return \atk4\ui\View
     */
    public function addModal($button, $title, $callback, $owner = null, $args = [])
    {
        $owner = $owner ?: $this->owner->owner;

        $modal = \atk4\ui\Modal::addTo($owner, [is_array($title) ? $title : compact('title'), 'appStickyCb' => true]);

        $modal->observeChanges(); // adds scrollbar if needed

        $modal->set(function ($t) use ($callback) {
            call_user_func($callback, $t, $this->app->stickyGet($this->name));
        });

        return $this->addButton($button, $modal->show(array_merge([$this->name=>$this->owner->jsRow()->data('id')], $args)));
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

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        if (!$this->buttons) {
            return '';
        }

        // render our buttons
        $output = '';
        foreach ($this->buttons as $button) {
            $output .= $button->getHTML();
        }

        return '<div class="ui buttons">' . $output . '</div>';
    }

    public function getHTMLTags($row, $field)
    {
        $tags = [];
        foreach ($this->callbacks as $name => $callback) {
            // if action is enabled then do not set disabled class
            if (call_user_func($callback, $row)) {
                continue;
            }

            $tags['_' . $name . '_disabled'] = 'disabled';
        }

        return $tags;
    }

    // rest will be implemented for crud
}
