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
     * @param mixed $name      Name of tab or Tab object
     * @param mixed $action    Optional callback action or URL (or array with url + parameters)
     * @param bool  $needJsURL Whether the virtual page should generate a jsURL or not.
     *
     * @return View
     */
    public function addTab($name = null, $action = null, $needJsURL = false)
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
        $item = $this->add([$item, 'class' => ['item']], 'Menu');
        $item->setElement('a');
        $item->setAttr('data-tab', $item->name);

        // add tabs sub-view
        $sub = $this->add(['View', 'class' => ['ui tab']], 'Tabs');
        $sub->setAttr('data-tab', $item->name);

        if ($action) {
            if (is_callable($action)) {
                // if there is callback action, then use VirtualPage
                $vp = $sub->add('VirtualPage');
                $vp->cb->needJsURL = $needJsURL;
                $item->setPath($vp->getUrl('cut'));

                $vp->set($action);
            } else {
                // otherwise treat it as URL
                //# TODO: refactor this ugly hack
                $item->setPath(str_replace('.php.', '.', $this->url($action)).'#');
            }
        }

        return $sub;
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
