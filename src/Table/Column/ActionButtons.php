<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\JsChain;
use Atk4\Ui\Table;
use Atk4\Ui\UserAction\ExecutorInterface;

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
     * @param \Atk4\Ui\View|string               $button
     * @param JsChain|\Closure|ExecutorInterface $action
     *
     * @return \Atk4\Ui\View
     */
    public function addButton($button, $action = null, string $confirmMsg = '', $isDisabled = false)
    {
        $name = $this->name . '_button_' . (count($this->buttons) + 1);

        if (!is_object($button)) {
            if (is_string($button)) {
                $button = [1 => $button];
            }

            $button = Factory::factory([\Atk4\Ui\Button::class], Factory::mergeSeeds($button, ['id' => false]));
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
     * @param \Atk4\Ui\View|string $button
     * @param string|array         $defaults modal title or modal defaults array
     * @param \Atk4\Ui\View        $owner
     * @param array                $args
     *
     * @return \Atk4\Ui\View
     */
    public function addModal($button, $defaults, \Closure $callback, $owner = null, $args = [])
    {
        $owner = $owner ?: $this->getOwner()->getOwner();

        if (is_string($defaults)) {
            $defaults = ['title' => $defaults];
        }

        $defaults['appStickyCb'] = true;

        $modal = \Atk4\Ui\Modal::addTo($owner, $defaults);

        $modal->observeChanges(); // adds scrollbar if needed

        $modal->set(function ($t) use ($callback) {
            $callback($t, $this->getApp()->stickyGet($this->name));
        });

        return $this->addButton($button, $modal->show(array_merge([$this->name => $this->getOwner()->jsRow()->data('id')], $args)));
    }

    public function getTag($position, $value, $attr = []): string
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $value, $attr);
    }

    public function getDataCellTemplate(\Atk4\Data\Field $field = null)
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
