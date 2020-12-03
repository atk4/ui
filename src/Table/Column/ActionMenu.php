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
use Atk4\Ui\View;

class ActionMenu extends Table\Column
{
    /**
     * Menu items collections.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Callbacks as defined in $action->enabled for evaluating row-specific if an action is enabled.
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * Dropdown label.
     * Note: In Grid::class, this value is set by ActionMenuDecorator property.
     *
     * @var string
     */
    public $label;

    /**
     * Dropdown module css class name as per Formantic-ui.
     *
     * @var string
     */
    public $ui = 'ui small dropdown button';

    /**
     * The dropdown module option setting as per Fomantic-ui.
     *
     * @var array
     */
    public $options = ['action' => 'hide'];

    /**
     * Button icon to use for display dropdown.
     *
     * @var string
     */
    public $icon = 'dropdown icon';

    protected function init(): void
    {
        parent::init();
    }

    public function getTag($position, $value, $attr = [])
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $value, $attr);
    }

    /**
     * Add a menu item in Dropdown.
     *
     * @param View|string                    $item
     * @param \Closure|Model\UserAction|null $action
     *
     * @return object|string
     */
    public function addActionMenuItem($item, $action = null, string $confirmMsg = '', bool $isDisabled = false)
    {
        // If action is not specified, perhaps it is defined in the model
        if (!$action) {
            if (is_string($item)) {
                $action = $this->table->model->getUserAction($item);
            } elseif ($item instanceof Model\UserAction) {
                $action = $item;
            }

            if ($action) {
                $item = $action->caption;
            }
        }

        $name = $this->name . '_action_' . (count($this->items) + 1);

        if ($action instanceof Model\UserAction) {
            $confirmMsg = $action->ui['confirm'] ?? $confirmMsg;

            $isDisabled = !$action->enabled;

            if ($action->enabled instanceof \Closure) {
                $this->callbacks[$name] = $action->enabled;
            }
        }

        if (!is_object($item)) {
            $item = Factory::factory([\Atk4\Ui\View::class], ['id' => false, 'ui' => 'item', 'content' => $item]);
        }

        $this->items[] = $item;

        $item->addClass('{$_' . $name . '_disabled} i_' . $name);

        if ($isDisabled) {
            $item->addClass('disabled');
        }

        // set executor context.
        $context = (new Jquery())->closest('.ui.button');

        $this->table->on('click', '.i_' . $name, $action, [$this->table->jsRow()->data('id'), 'confirm' => $confirmMsg, 'apiConfig' => ['stateContext' => $context]]);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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
