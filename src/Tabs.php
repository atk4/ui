<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Tabs widget.
 */
class Tabs extends View
{
    public $ui = 'tabular menu';

    public $defaultTemplate = 'tabs.html';

    /**
     * Adds tab in tabs widget.
     *
     * @param mixed    $name   Name of tab or Tab object
     * @param callable $action Optional callback action
     *
     * @return View
     */
    public function addTab($name = null, $action = null)
    {
        // add tabs menu item
        if (is_object($name)) {
            $item = $name;
        } elseif ($name) {
            $item = new Tab($name);
        } else {
            $item = new Tab();
        }

        // add tabs menu item
        $item = $this->add([$item, 'class'=>['item']], 'Menu');
        $item->setAttr('data-tab', $item->name);

        // add tabs sub-view
        $sub = $this->add(['View', 'class'=>['ui tab']], 'Tabs');
        $sub->setAttr('data-tab', $item->name);

        return  $sub;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        // activate first tab
        $this->js(true)->find('.menu .item')->first()->addClass('active');
        $this->js(true)->find('.tab')->first()->addClass('active');

        // initialize JS tabs
        $this->js(true)->find('.item')->tab();

        // use content as class name
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
