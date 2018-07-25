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

    public $selectedTabName = null;

    /**
     * Adds tab in tabs widget.
     *
     * @param mixed $name     Name of tab or Tab object
     * @param mixed $callback Callback action or URL (or array with url + parameters)
     * @param bool  $selected Determines if the current tab is set as selected or not <
     *
     * @throws Exception
     *
     * @return View
     */
    public function addTab($name, $callback = null, $selected = false)
    {
        $item = $this->addTabMenuItem($name);
        $sub = $this->addSubView($item->name);

        // Set the first tab as selected, or change to the current tab <
        if ($selected || empty($this->selectedTabName)) {
            $this->selectedTabName = $item->name;
        }

        if ($callback) {
            // if there is callback action, then use VirtualPage
            $vp = $sub->add('VirtualPage');
            $item->setPath($vp->getJSURL('cut'));

            $vp->set($callback);

            return;
        }

        return $sub;
    }

    /**
     * Adds dynamic tab in tabs widget which will load a separate
     * page/url when activated.
     *
     * @param mixed        $name Name of tab or Tab object
     * @param string|array $url  URL to open inside a tab
     *
     * @throws Exception
     */
    public function addTabURL($name, $url)
    {
        $item = $this->addTabMenuItem($name);
        $sub = $this->addSubView($item->name);

        $item->setPath($url);
    }

    /**
     * Add a tab menu item.
     *
     * @param string $name Name of tab or Tab object.
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
        $this->js(true)->find('#'.$this->selectedTabName)->addClass('active');
        $this->js(true)->find('.tab[data-tab="'.$this->selectedTabName.'"]')->addClass('active');

        // use content as class name
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
