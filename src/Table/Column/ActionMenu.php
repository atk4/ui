<?php

declare(strict_types=1);
/**
 * Table column action menu.
 * Will create a dropdown menu within table column.
 */

namespace Atk4\Ui\Table\Column;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsChain;
use Atk4\Ui\Table;
use Atk4\Ui\UserAction\ExecutorInterface;
use Atk4\Ui\View;

class ActionMenu extends Table\Column
{
    /** @var array Menu items collections. */
    protected $items = [];

    /** @var array Callbacks as defined in UserAction->enabled for evaluating row-specific if an action is enabled. */
    protected $callbacks = [];

    /**
     * Dropdown label.
     * Note: In Grid::class, this value is set by ActionMenuDecorator property.
     *
     * @var string
     */
    public $label;

    /** @var string Dropdown module css class name as per Formantic-ui. */
    public $ui = 'ui small dropdown button';

    /** @var array The dropdown module option setting as per Fomantic-ui. */
    public $options = ['action' => 'hide'];

    /** @var string Button icon to use for display dropdown. */
    public $icon = 'dropdown icon';

    protected function init(): void
    {
        parent::init();
    }

    public function getTag($position, $value, $attr = []): string
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $value, $attr);
    }

    /**
     * Add a menu item in Dropdown.
     *
     * @param View|string                           $item
     * @param \Closure|Model|ExecutorInterface|null $action
     *
     * @return object|string
     */
    public function addActionMenuItem($item, $action = null, string $confirmMsg = '', $isDisabled = false)
    {
        $name = $this->name . '_action_' . (count($this->items) + 1);

        if (!is_object($item)) {
            $item = Factory::factory([\Atk4\Ui\View::class], ['name' => false, 'ui' => 'item', 'content' => $item]);
        }

        $this->items[] = $item;

        $item->addClass('{$_' . $name . '_disabled} i_' . $name);

        if ($isDisabled === true) {
            $item->addClass('disabled');
        }

        if (is_callable($isDisabled)) {
            $this->callbacks[$name] = $isDisabled;
        }

        // set executor context.
        $context = (new Jquery())->closest('.ui.button');

        $this->table->on('click', '.i_' . $name, $action, [$this->table->jsRow()->data('id'), 'confirm' => $confirmMsg, 'apiConfig' => ['stateContext' => $context]]);

        return $item;
    }

    public function getHeaderCellHtml(\Atk4\Data\Field $field = null, $value = null)
    {
        $this->table->js(true)->find('.atk-action-menu')->dropdown(
            array_merge(
                $this->options,
                [
                    'direction' => 'auto',  // direction need to be auto.
                    'transition' => 'none', // no transition.
                    'onShow' => (new JsChain('atk.tableDropdown.onShow')),
                    'onHide' => (new JsChain('atk.tableDropdown.onHide')),
                ]
            )
        );

        return parent::getHeaderCellHtml($field, $value);
    }

    public function getDataCellTemplate(\Atk4\Data\Field $field = null)
    {
        if (!$this->items) {
            return '';
        }

        // render our menus
        $output = '';
        foreach ($this->items as $item) {
            $output .= $item->getHtml();
        }

        $s = '<div class="' . $this->ui . ' atk-action-menu">';
        $s .= '<div class="text">' . $this->label . '</div>';
        $s .= $this->icon ? '<i class="' . $this->icon . '"></i>' : '';
        $s .= '<div class="menu">';
        $s .= $output;
        $s .= '</div></div>';

        return $s;
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
}
