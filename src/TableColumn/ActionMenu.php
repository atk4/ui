<?php
/**
 * Table column action menu.
 * Will create a dropdown menu within table column.
 */

namespace atk4\ui\TableColumn;

use atk4\core\FactoryTrait;
use atk4\ui\jQuery;
use atk4\ui\jsChain;

class ActionMenu extends Generic
{
    use FactoryTrait;

    /**
     * Menu items collections.
     *
     * @var array
     */
    public $actions = [];

    /**
     * Dropdown label.
     * Note: In Grid::class, this value is set by ActionMenuDecorator property.
     *
     * @var null
     */
    public $label = null;

    /**
     * Dropdown module css class name as per Formantic-ui.
     *
     * @var string
     */
    public $ui = 'ui small floating dropdown button';

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

    public function init()
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
     * @param $item
     * @param null $callback
     * @param null $confirm
     * @param bool $isDisabled
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return object|string
     */
    public function addActionMenuItem($item, $callback = null, $confirm = null, $isDisabled = false)
    {
        // If action is not specified, perhaps it is defined in the model
        if (!$callback && is_string($item)) {
            $model_action = $this->table->model->getAction($item);
            if ($model_action) {
                $isDisabled = !$model_action->enabled;
                $callback = $model_action;
                $item = $callback->caption;
                if ($model_action->ui['confirm'] ?? null) {
                    $confirm = $model_action->ui['confirm'];
                }
            }
        } elseif (!$callback && $item instanceof \atk4\data\UserAction\Generic) {
            $isDisabled = !$item->enabled;
            if ($item->ui['confirm'] ?? null) {
                $confirm = $item->ui['confirm'];
            }
            $callback = $item;
            $item = $item->caption;
        }

        $name = $this->name.'_action_'.(count($this->actions) + 1);

        if (!is_object($item)) {
            $item = $this->factory('View', ['id' => false, 'ui' => 'item', 'content' => $item], 'atk4\ui');
        }

        $this->actions[] = $item;
        $item->addClass('i_'.$name);
        if ($isDisabled) {
            $item->addClass('disabled');
        }

        // set executor context.
        $context = (new jQuery())->closest('.ui.button');

        $this->table->on('click', '.i_'.$name, $callback, [$this->table->jsRow()->data('id'), 'confirm' => $confirm, 'apiConfig' => ['stateContext' => $context]]);

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
                    'direction'  => 'auto',  // direction need to be auto.
                    'transition' => 'none', // no transition.
                    'onShow'     => (new jsChain('atk.tableDropdown.onShow')),
                    'onHide'     => (new jsChain('atk.tableDropdown.onHide')),
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
        if (!$this->actions) {
            return '';
        }

        // render our menus
        $output = '';
        foreach ($this->actions as $item) {
            $output .= $item->getHTML();
        }

        $s = '<div class="'.$this->ui.' atk-action-menu">';
        $s .= '<div class="text">'.$this->label.'</div>';
        $s .= $this->icon ? '<i class="'.$this->icon.'"></i>' : '';
        $s .= '<div class="menu">';
        $s .= $output;
        $s .= '</div></div>';

        return $s;
    }
}
