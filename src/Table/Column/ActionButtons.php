<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Core\Factory;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Modal;
use Atk4\Ui\Table;
use Atk4\Ui\UserAction\ExecutorInterface;
use Atk4\Ui\View;

/**
 * Formatting action buttons column.
 *
 * @phpstan-type JsCallbackSetClosure \Closure(Jquery, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): (JsExpressionable|View|string|void)
 */
class ActionButtons extends Table\Column
{
    /** @var array Stores all the buttons that have been added. */
    public $buttons = [];

    /** @var array<string, \Closure(Model): bool> Callbacks as defined in UserAction->enabled for evaluating row-specific if an action is enabled. */
    protected $callbacks = [];

    protected function init(): void
    {
        parent::init();

        $this->addClass('right aligned');
    }

    /**
     * Adds a new button which will execute $callback when clicked.
     *
     * @param string|array|View                                       $button
     * @param JsExpressionable|JsCallbackSetClosure|ExecutorInterface $action
     * @param bool|\Closure(Model): bool                              $isDisabled
     *
     * @return View
     */
    public function addButton($button, $action = null, string $confirmMsg = '', $isDisabled = false)
    {
        $name = $this->name . '_button_' . (count($this->buttons) + 1);

        if (!is_object($button)) {
            if (is_string($button)) {
                $button = [1 => $button];
            }

            $button = Factory::factory([Button::class], Factory::mergeSeeds($button, ['name' => false]));
        }

        if ($isDisabled === true) {
            $button->addClass('disabled');
        } elseif ($isDisabled !== false) {
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
     * @param string|array|View                 $button
     * @param string|array                      $defaults modal title or modal defaults array
     * @param \Closure(View, string|null): void $callback
     * @param View                              $owner
     * @param array                             $args
     *
     * @return View
     */
    public function addModal($button, $defaults, \Closure $callback, $owner = null, $args = [])
    {
        if ($owner === null) { // TODO explicit owner should not be needed
            $owner = $this->getOwner()->getOwner();
        }

        if (is_string($defaults)) {
            $defaults = ['title' => $defaults];
        }

        $modal = Modal::addTo($owner, $defaults);

        $modal->set(function (View $t) use ($callback) {
            $callback($t, $t->stickyGet($this->name));
        });

        return $this->addButton($button, $modal->jsShow(array_merge([$this->name => $this->getOwner()->jsRow()->data('id')], $args)));
    }

    public function getTag(string $position, $value, $attr = []): string
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $value, $attr);
    }

    public function getDataCellTemplate(Field $field = null): string
    {
        if (count($this->buttons) === 0) {
            return '';
        }

        // render our buttons
        $outputHtml = '';
        foreach ($this->buttons as $button) {
            $outputHtml .= $button->getHtml();
        }

        return $this->getApp()->getTag('div', ['class' => 'ui buttons'], [$outputHtml]);
    }

    public function getHtmlTags(Model $row, ?Field $field): array
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
