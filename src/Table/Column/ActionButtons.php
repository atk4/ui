<?php

declare(strict_types=1);

namespace atk4\ui\Table\Column;

use atk4\core\Factory;
use atk4\data\Model;
use atk4\ui\Button;
use atk4\ui\JsChain;
use atk4\ui\Table;
use atk4\ui\UserAction\ExecutorInterface;

/**
 * Formatting action buttons column.
 */
class ActionButtons extends Table\Column
{
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

    protected function init(): void
    {
        parent::init();
        $this->addClass('right aligned');
    }

    /**
     * Adds a new button which will execute $callback when clicked.
     *
     * Returns button object
     *
     * @param \atk4\ui\View|string               $button
     * @param JsChain|\Closure|ExecutorInterface $action
     *
     * @return \atk4\ui\View
     */
    public function addButton($button, $action = null, string $confirmMsg = '', $isDisabled = false)
    {
        $name = $this->name . '_button_' . (count($this->buttons) + 1);

        if (!is_object($button)) {
            if (is_string($button)) {
                $button = [1 => $button];
            }

            $button = Factory::factory([\atk4\ui\Button::class], Factory::mergeSeeds($button, ['id' => false]));
        }

        if ($isDisabled === true) {
            $button->addClass('disabled');
        }

        if (is_callable($isDisabled)) {
            $this->callbacks[$name] = $isDisabled;
        }

        $button->setApp($this->table->getApp());

        $this->buttons[$name] = $button->addClass('{$_' . $name . '_disabled} compact b_' . $name);

        $this->table->on('click', '.b_' . $name, $action, [$this->table->jsRow()->data('id'), 'confirm' => $confirmMsg]);

        return $button;
    }

    /**
     * Adds a new button which will open a modal dialog and dynamically
     * load contents through $callback. Will pass a virtual page.
     *
     * @param \atk4\ui\View|string $button
     * @param string|array         $defaults modal title or modal defaults array
     * @param \atk4\ui\View        $owner
     * @param array                $args
     *
     * @return \atk4\ui\View
     */
    public function addModal($button, $defaults, \Closure $callback, $owner = null, $args = [])
    {
        $owner = $owner ?: $this->getOwner()->getOwner();

        if (is_string($defaults)) {
            $defaults = ['title' => $defaults];
        }

        $defaults['appStickyCb'] = true;

        $modal = \atk4\ui\Modal::addTo($owner, $defaults);

        $modal->observeChanges(); // adds scrollbar if needed

        $modal->set(function ($t) use ($callback) {
            $callback($t, $this->getApp()->stickyGet($this->name));
        });

        return $this->addButton($button, $modal->show(array_merge([$this->name => $this->getOwner()->jsRow()->data('id')], $args)));
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

    public function getDataCellTemplate(\atk4\data\Field $field = null)
    {
        if (!$this->buttons) {
            return '';
        }

        // render our buttons
        $output = '';
        foreach ($this->buttons as $button) {
            $output .= $button->getHtml();
        }

        return '<div class="ui buttons">' . $output . '</div>';
    }

    public function getHtmlTags(Model $row, $field)
    {
        $tags = [];
        foreach ($this->callbacks as $name => $callback) {
            // if action is enabled then do not set disabled class
            if ($callback($row)) {
                continue;
            }

            $tags['_' . $name . '_disabled'] = 'disabled';
        }

        return $tags;
    }

    // rest will be implemented for crud
}
