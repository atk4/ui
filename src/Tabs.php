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
     * @param mixed $callback    Callback action or URL (or array with url + parameters)
     *
     * @throws Exception
     *
     * @return View
     */
    public function addTab($name, $callback = null)
    {
        $item = $this->addTabMenuItem($name);
        $sub = $this->addSubView($item->name);

        if ($callback) {
            // if there is callback action, then use VirtualPage
            $vp = $sub->add('VirtualPage');
            $item->setPath($vp->getUrl('cut'));

            $vp->set($callback);

            return null;
        }


        return $sub;
    }

    /**
     * Adds dynamic tab in tabs widget.
     * Dynamic tabs are loaded via a virtual page callback or url.
     *
     * @param mixed $name      Name of tab or Tab object
     * @param url   $needJsURL Supply URL of another page which will open in the tab
     *
     * @throws Exception
     */
    public function addTabURL($name, $url)
    {
        $item = $this->addTabMenuItem($name);
        $sub = $this->addSubView($item->name);

        $item->setPath($url);

        return null;
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

        return $this->add([$tab, 'class' => ['item']], 'Menu')
                ->setElement('a')
                ->setAttr('data-tab', $tab->name);
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
        return $this->add(['View', 'class' => ['ui tab']], 'Tabs')->setAttr('data-tab', $name);
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
