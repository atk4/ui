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
    /** @var array<string, View> Stores all the buttons that have been added. */
    public $buttons = [];

    /** @var array<string, \Closure<T of Model>(T): bool> Callbacks as defined in UserAction->enabled for evaluating row-specific if an action is enabled. */
    protected $isEnabledFxs = [];

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->addClass('right aligned');
    }

    /**
     * Adds a new button which will execute $action when clicked.
     *
     * @param string|array|View                                       $button
     * @param JsExpressionable|JsCallbackSetClosure|ExecutorInterface $action
     * @param bool|\Closure<T of Model>(T): bool                      $isDisabled
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

            $button = Factory::factory([Button::class], $button);
        }

        $this->assertColumnViewNotInitialized($button);

        if ($isDisabled === true) {
            $button->addClass('disabled');
        } elseif ($isDisabled !== false) {
            $this->isEnabledFxs[$name] = $isDisabled;
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
     * @param string|array|View                  $button
     * @param string|array                       $defaults   modal title or modal defaults array
     * @param \Closure(View, mixed): void        $callback
     * @param View                               $owner
     * @param array                              $args
     * @param bool|\Closure<T of Model>(T): bool $isDisabled
     *
     * @return View
     */
    public function addModal($button, $defaults, \Closure $callback, $owner = null, $args = [], $isDisabled = false)
    {
        if ($owner === null) { // TODO explicit owner should not be needed
            $owner = $this->getOwner()->getOwner();
        }

        if (is_string($defaults)) {
            $defaults = ['title' => $defaults];
        }

        $modal = Modal::addTo($owner, $defaults);

        $modal->set(function (View $t) use ($callback) {
            $id = $this->getApp()->uiPersistence->typecastAttributeLoadField($this->table->model->getIdField(), $t->stickyGet($this->name));
            $callback($t, $id);
        });

        return $this->addButton($button, $modal->jsShow(array_merge([$this->name => $this->getOwner()->jsRow()->data('id')], $args)), '', $isDisabled);
    }

    #[\Override]
    public function getTag(string $position, $attr, $value): string
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $attr, $value);
    }

    #[\Override]
    public function getDataCellTemplate(Field $field = null): string
    {
        if (count($this->buttons) === 0) {
            return '';
        }

        // render our buttons
        $outputHtmls = [];
        foreach ($this->buttons as $name => $button) {
            $button = $this->cloneColumnView($button, $this->table->currentRow, $name);
            $outputHtmls[] = $button->getHtml();
        }

        return $this->getApp()->getTag('div', ['class' => 'ui buttons'], $outputHtmls);
    }

    #[\Override]
    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $tags = [];
        foreach ($this->isEnabledFxs as $name => $isEnabledFx) {
            if (!$isEnabledFx($row)) {
                $tags['_' . $name . '_disabled'] = 'disabled';
            }
        }

        return $tags;
    }
}
