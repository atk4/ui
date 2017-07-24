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
        $item->setAttr('data-tab', $item->name);

        // add tabs sub-view
        $sub = $this->add(['View', 'class'=>['ui tab']], 'Tabs');
        $sub->setAttr('data-tab', $item->name);

        // if there is callback action, then
        if ($action && is_callable($action)) {
            $vp = $sub->add('VirtualPage');
            $sub->setAttr('data-url', $vp->getURL());

            $vp->set(function () use ($vp, $action) {
                $action($vp);
                $this->app->terminate($vp->render());
            });
        }

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
        // https://github.com/Semantic-Org/Semantic-UI/issues/2535
        // https://stackoverflow.com/a/33532195/1466341
        $this->js(true)->find('.item')->tab([
            'cache'   => false,
            'context' => 'parent',
            //'auto' => true,
            'apiSettings' => [
                'loadingDuration' => 300,
                //'url' => 'http://www.google.lv',
            ],
        ]);

        // use content as class name
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
