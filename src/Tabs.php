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

    /** @var string name of active tab */
    public $activeTabName = null;

    /**
     * Adds tab in tabs widget.
     *
     * @param string|Tab $name     Name of tab or Tab object
     * @param callable   $callback Callback action or URL (or array with url + parameters)
     *
     * @throws Exception
     *
     * @return View
     */
    public function addTab($name, $callback = null)
    {
        $item = $this->addTabMenuItem($name);
        $sub = $this->addSubView($item->name);

        // if there is callback action, then use VirtualPage
        if ($callback) {
            $vp = $sub->add(['VirtualPage', 'ui' => '']);
            $item->setPath($vp->getJSURL('cut'));

            $vp->set($callback);
        }

        return $sub;
    }

    /**
     * Adds dynamic tab in tabs widget which will load a separate
     * page/url when activated.
     *
     * @param string|Tab   $name Name of tab or Tab object
     * @param string|array $url  URL to open inside a tab
     *
     * @throws Exception
     */
    public function addTabURL($name, $url)
    {
        $item = $this->addTabMenuItem($name);
        $this->addSubView($item->name);

        $item->setPath($url);
    }

    /**
     * Add a tab menu item.
     *
     * @param string|Tab $name Name of tab or Tab object.
     *
     * @throws Exception
     *
     * @return Tab|View Tab menu item view.
     */
    protected function addTabMenuItem($name)
    {
        if (is_object($name)) {
            $tab = $name;
        } else {
            $tab = new Tab($name);
        }

        $tab = $this->add([$tab, 'class' => ['item']], 'Menu')
            ->setElement('a')
            ->setAttr('data-tab', $tab->name);

        if (empty($this->activeTabName)) {
            $this->activeTabName = $tab->name;
        }

        return $tab;
    }

    /**
     * Add sub view to tab.
     *
     * @param string $name name of view.
     *
     * @throws Exception
     *
     * @return TabsSubView
     */
    protected function addSubView($name)
    {
        return $this->add(['TabsSubView', 'dataTabName' => $name], 'Tabs');
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        // use content as class name
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
