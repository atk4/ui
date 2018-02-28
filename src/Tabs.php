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
     * @param mixed $name Name of tab or Tab object
     *
     * @throws Exception
     *
     * @return View
     */
    public function addTab($name)
    {
        return $this->addSubView($this->addTabMenuItem($name)->name);
    }

    /**
     * Adds dynamic tab in tabs widget.
     * Dynamic tabs are loaded via a virtual page callback or url.
     *
     * @param mixed $name      Name of tab or Tab object
     * @param mixed $action    Callback action or URL (or array with url + parameters)
     * @param bool  $needJsURL Whether the virtual page should generate a jsURL or not.
     *
     * @throws Exception
     */
    public function addTabURL($name, $action, $needJsURL = false)
    {
        $item = $this->addTabMenuItem($name);
        $sub = $this->addSubView($item->name);

        if (is_callable($action)) {
            // if there is callback action, then use VirtualPage
            $vp = $sub->add('VirtualPage');
            $vp->cb->needJsURL = $needJsURL;
            $item->setPath($vp->getUrl('cut'));

            $vp->set($action);
        } else {
            // otherwise treat it as URL
            //# TODO: refactor this ugly hack
            $item->setPath(str_replace('.php.', '.', ($needJsURL) ? $this->jsURL($action) : $this->url($action)).'#');
        }
    }

    /**
     * Add a tab menu item.
     *
     * @param $name Name of tab or Tab object.
     *
     * @throws Exception
     *
     * @return Tab|View Tab menu item view.
     */
    private function addTabMenuItem($name)
    {
        if (is_object($name)) {
            $tab = $name;
        } else {
            $tab = new Tab($name);
        }

        $item = $this->add([$tab, 'class' => ['item']], 'Menu');
        $item->setElement('a');
        $item->setAttr('data-tab', $tab->name);

        return $item;
    }

    /**
     * Add sub view to tab.
     *
     * @param string $name name of view.
     *
     * @throws Exception
     *
     * @return View
     */
    private function addSubView($name)
    {
        $sub = $this->add(['View', 'class' => ['ui tab']], 'Tabs');
        $sub->setAttr('data-tab', $name);

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
