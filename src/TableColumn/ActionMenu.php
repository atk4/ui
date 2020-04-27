<?php
/**
 * Table column action menu.
 * Will create a dropdown menu within table column.
 */

namespace atk4\ui\TableColumn;

use atk4\core\FactoryTrait;
use atk4\ui\jQuery;
use atk4\ui\jsChain;
use atk4\ui\View;

class ActionMenu extends Generic
{
    use FactoryTrait;

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
     * @var null
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

    public function init(): void
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
     * @param View|string                                 $item
     * @param callable|\atk4\data\UserAction\Generic|null $action
     * @param string|null                                 $confirm
     * @param bool                                        $isDisabled
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return object|string
     */
    public function addActionMenuItem($item, $action = null, $confirm = null, $isDisabled = false)
    {
        // If action is not specified, perhaps it is defined in the model
        if (!$action) {
            if (is_string($item)) {
                $action = $this->table->model->getAction($item);
            } elseif ($item instanceof \atk4\data\UserAction\Generic) {
                $action = $item;
            }

            if ($action) {
                $item = $action->caption;
            }
        }

        $name = $this->name . '_action_' . (count($this->items) + 1);

        if ($action instanceof \atk4\data\UserAction\Generic) {
            $confirm = $action->ui['confirm'] ?? $confirm;

            $isDisabled = !$action->enabled;

            if (is_callable($action->enabled)) {
                $this->callbacks[$name] = $action->enabled;
            }
        }

        if (!is_object($item)) {
            $item = $this->factory('View', ['id' => false, 'ui' => 'item', 'content' => $item], 'atk4\ui');
        }

        $this->items[] = $item;

        $item->addClass('{$_' . $name . '_disabled} i_' . $name);

        if ($isDisabled) {
            $item->addClass('disabled');
        }

        // set executor context.
        $context = (new jQuery())->closest('.ui.button');

        $this->table->on('click', '.i_' . $name, $action, [$this->table->jsRow()->data('id'), 'confirm' => $confirm, 'apiConfig' => ['stateContext' => $context]]);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderCellHTML(\atk4\data\Field $f = null, $value = null)
    {
        $this->table->js(true)->find('.atk-action-menu')->dropdown(
            array_merge(
                $this->options,
                [
                    'direction' => 'auto',  // direction need to be auto.
                    'transition' => 'none', // no transition.
                    'onShow' => (new jsChain('atk.tableDropdown.onShow')),
                    'onHide' => (new jsChain('atk.tableDropdown.onHide')),
                ]
            )
        );

        return parent::getHeaderCellHTML($f, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        if (!$this->items) {
            return '';
        }

        // render our menus
        $output = '';
        foreach ($this->items as $item) {
            $output .= $item->getHTML();
        }

        $s = '<div class="' . $this->ui . ' atk-action-menu">';
        $s .= '<div class="text">' . $this->label . '</div>';
        $s .= $this->icon ? '<i class="' . $this->icon . '"></i>' : '';
        $s .= '<div class="menu">';
        $s .= $output;
        $s .= '</div></div>';

        return $s;
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
}
