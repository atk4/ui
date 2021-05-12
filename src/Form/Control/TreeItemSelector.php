<?php

declare(strict_types=1);
/**
 * Display items in a hierarchical (tree) view structure.
 *
 * When an item contains nodes with non empty values, it will automatically be treat as a group level;
 *
 * The input value is store as an array type when allowMultiple is set to true, otherwise, will
 * store one single value when set to false.
 *
 * Only item id are store within the input field.
 *
 * see demos/tree-item-selector.php to see how tree items are build.
 */

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Form;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\JsCallback;

class TreeItemSelector extends Form\Control
{
    /**
     * Template for the item selector view.
     *
     * @var HtmlTemplate
     */
    public $itemSelectorTemplate;

    /**
     * The tree item selector View.
     *
     * @var \Atk4\Ui\View|null
     */
    public $itemSelector;

    /**
     * The css class selector for where to apply loading class name.
     * Loading class name is set during on Item callback.
     *
     * @var string
     */
    public $loaderCssName = 'atk-tree-loader';

    /**
     * Allow multiple selection or just one.
     *
     * @var bool
     */
    public $allowMultiple = true;

    /**
     * The list of items.
     * Item must have at least one name and one id.
     * Only the id value, from a single node, are returned i.e. not the group id value.
     *
     * Each item may have it's own children by adding nodes children to it.
     *   $items = [
     *       ['name' => 'Electronics', 'id' => 'P100', 'nodes' => [['name' => 'Phone', 'id' => 'P100', 'nodes' => [['name' => 'iPhone', 'id' => 502,], ['name' => 'Google Pixels', 'id' => 503]]], ['name' => 'Tv' , 'id' => 501], ['name' => 'Radio' , 'id' => 601]]],
     *       ['name' => 'Cleaner' , 'id' => 201],
     *       ['name' => 'Appliances' , 'id' => 301]
     *   ];
     *
     * When adding nodes array into an item, it will automatically be treated as a group unless empty.
     *
     * @var array
     */
    public $treeItems = [];

    /**
     * Callback for onTreeChange.
     *
     * @var JsCallback|null
     */
    private $cb;

    protected function init(): void
    {
        parent::init();

        $this->addClass(['ui', 'vertical', 'segment', 'basic', $this->loaderCssName])->addStyle(['padding' => '0px!important']);

        if (!$this->itemSelectorTemplate) {
            $this->itemSelectorTemplate = new HtmlTemplate('<div id="{$_id}" class="ui list" style="margin-left: 16px"><atk-tree-item-selector v-bind="initData"></atk-tree-item-selector><div class="ui hidden divider"></div>{$Input}</div>');
        }

        $this->itemSelector = \Atk4\Ui\View::addTo($this, ['template' => $this->itemSelectorTemplate]);
    }

    /**
     * Provide a function to be execute when clicking an item in tree selector.
     * The executing function will receive an array with item state in it
     * when allowMultiple is true or a single value when false.
     *
     * @return $this
     */
    public function onItem(\Closure $fx)
    {
        $this->cb = JsCallback::addTo($this)->set(function ($j, $data) use ($fx) {
            $value = $this->getApp()->decodeJson($data);
            if (!$this->allowMultiple) {
                $value = $value[0];
            }

            return $fx($value);
        }, ['data' => 'value']);

        return $this;
    }

    /**
     * Set the items.
     *
     * @return $this
     */
    public function setTreeItems($treeItems)
    {
        $this->treeItems = $treeItems;

        return $this;
    }

    /**
     * Input field.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->getApp()->getTag('input', [
            'name' => $this->short_name,
            'type' => 'hidden',
            'value' => $this->getValue(),
            'readonly' => true,
        ]);
    }

    public function getValue()
    {
        return $this->getApp()->ui_persistence->typecastSaveField($this->field, $this->field->get());
    }

    protected function renderView(): void
    {
        parent::renderView();

        $this->itemSelector->template->tryDangerouslySetHtml('Input', $this->getInput());

        $this->itemSelector->vue(
            'atk-tree-item-selector',
            [
                'item' => ['id' => 'atk-root', 'nodes' => $this->treeItems],
                'values' => [], // need empty for Vue reactivity.
                'field' => $this->short_name,
                'options' => [
                    'mode' => $this->allowMultiple ? 'multiple' : 'single',
                    'url' => $this->cb ? $this->cb->getJsUrl() : null,
                    'loader' => $this->loaderCssName,
                ],
            ]
        );
    }
}
