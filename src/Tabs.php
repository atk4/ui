<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Tabs widget.
 */
class Tabs extends View
{
    public $defaultTemplate = 'tabs.html';
    public $ui = 'tabular menu';

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
        $item->setElement('a');
        $item->setAttr('data-tab', $item->name);

        // add tabs sub-view
        $sub = $this->add(['View', 'class'=>['ui tab']], 'Tabs');
        $sub->setAttr('data-tab', $item->name);

        // if there is callback action, then
        if ($action && is_callable($action)) {
            $vp = $sub->add('VirtualPage');
            $item->setPath($vp->getUrl());

            $vp->set(function () use ($vp, $action) {
                $action($vp);
                $this->app->terminate($vp->render());
            });
        }

        return  $sub;
    }

    public function addTabURL($name, $url)
    {
        $item = $this->add(['Tab', $name, 'class'=>['item']], 'Menu');
        $item->setAttr('data-tab', $item->name);

        // add tabs sub-view
        $sub = $this->add(['View', 'class'=>['ui tab']], 'Tabs');
        $sub->setAttr('data-tab', $item->name);

        //# TODO: refactor this ugly hack
        $item->setPath(str_replace('.php.', '.', $this->app->url($url)).'#');
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        // activate first tab
        $this->js(true)->find('.menu .item')->first()->addClass('active');
        $this->js(true)->find('.tab')->first()->addClass('active');

        // use content as class name
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
