<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Core\Factory;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Table;
use Atk4\Ui\UserAction\ExecutorInterface;
use Atk4\Ui\View;

/**
 * Table column action menu.
 * Will create a dropdown menu within table column.
 *
 * @phpstan-type JsCallbackSetClosure \Closure(Jquery, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): (JsExpressionable|View|string|void)
 */
class ActionMenu extends Table\Column
{
    /** @var array<int, View> Menu items collections. */
    protected $items = [];

    /** @var array<string, \Closure<T of Model>(T): bool> Callbacks as defined in UserAction->enabled for evaluating row-specific if an action is enabled. */
    protected $isEnabledFxs = [];

    /** @var string Dropdown label. */
    public $label;

    /** @var string Dropdown module CSS class name as per Formantic-UI. */
    public $ui = 'small dropdown button';

    /** @var array The dropdown module option setting as per Fomantic-UI. */
    public $options = ['action' => 'hide'];

    /** @var string Button icon to use for display dropdown. */
    public $icon = 'dropdown';

    #[\Override]
    public function getTag(string $position, $attr, $value): string
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $attr, $value);
    }

    /**
     * Add a menu item in Dropdown.
     *
     * @param View|string                                             $item
     * @param JsExpressionable|JsCallbackSetClosure|ExecutorInterface $action
     * @param bool|\Closure<T of Model>(T): bool                      $isDisabled
     *
     * @return View
     */
    public function addActionMenuItem($item, $action = null, string $confirmMsg = '', $isDisabled = false)
    {
        $name = $this->name . '_action_' . (count($this->items) + 1);

        if (!is_object($item)) {
            $item = Factory::factory([View::class], ['ui' => 'item link', 'content' => $item]);
        }

        $this->assertColumnViewNotInitialized($item);

        $item->setApp($this->getApp());
        $this->items[] = $item;

        $item->addClass('{$_' . $name . '_disabled} i_' . $name);

        if ($isDisabled === true) {
            $item->addClass('disabled');
        } elseif ($isDisabled !== false) {
            $this->isEnabledFxs[$name] = $isDisabled;
        }

        if ($action !== null) {
            // set executor context
            $context = (new Jquery())->closest('.ui.button');

            $this->table->on('click', '.i_' . $name, $action, [
                $this->table->jsRow()->data('id'),
                'confirm' => $confirmMsg,
                'apiConfig' => ['stateContext' => $context],
            ]);
        }

        return $item;
    }

    #[\Override]
    public function getHeaderCellHtml(?Field $field = null, $value = null): string
    {
        $this->table->js(true)->find('.atk-action-menu')->dropdown(
            array_merge(
                $this->options,
                [
                    'direction' => 'auto', // direction needs to be "auto"
                    'transition' => 'none', // no transition
                    'onShow' => (new JsChain('atk.tableDropdownHelper.onShow')),
                    'onHide' => (new JsChain('atk.tableDropdownHelper.onHide')),
                ]
            )
        );

        return parent::getHeaderCellHtml($field, $value);
    }

    #[\Override]
    public function getDataCellTemplate(?Field $field = null): string
    {
        if (!$this->items) {
            return '';
        }

        // render our menus
        $outputHtmls = [];
        foreach ($this->items as $k => $item) {
            $item = $this->cloneColumnView($item, $this->table->currentRow, (string) $k);
            $outputHtmls[] = $item->getHtml();
        }

        $res = $this->getApp()->getTag('div', ['class' => 'ui ' . $this->ui . ' atk-action-menu'], [
            ['div', ['class' => 'text'], $this->label],
            $this->icon ? $this->getApp()->getTag('i', ['class' => $this->icon . ' icon'], '') : '',
            ['div', ['class' => 'menu'], $outputHtmls],
        ]);

        return $res;
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
