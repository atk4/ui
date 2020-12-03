<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;

/**
 * Tabs widget.
 */
class Tabs extends View
{
    public $defaultTemplate = 'tabs.html';
    public $ui = 'tabular menu';

    /** @var string name of active tab */
    public $activeTabName;

    /**
     * Adds tab in tabs widget.
     *
     * @param string|Tab $name     Name of tab or Tab object
     * @param \Closure   $callback Callback action or URL (or array with url + parameters)
     * @param array      $settings Tab setting
     *
     * @return View
     */
    public function addTab($name, \Closure $callback = null, $settings = [])
    {
        $item = $this->addTabMenuItem($name, $settings);
        $sub = $this->addSubView($item->name);

        // if there is callback action, then use VirtualPage
        if ($callback) {
            $vp = VirtualPage::addTo($sub, ['ui' => '']);
            $item->setPath($vp->getJsUrl('cut'));

            $vp->set($callback);
        }

        return $sub;
    }

    /**
     * Adds dynamic tab in tabs widget which will load a separate
     * page/url when activated.
     *
     * @param string|Tab   $name     Name of tab or Tab object
     * @param string|array $url      URL to open inside a tab
     * @param array        $settings Tab setting
     */
    public function addTabUrl($name, $url, $settings = [])
    {
        $item = $this->addTabMenuItem($name, $settings);
        $this->addSubView($item->name);

        $item->setPath($url);
    }

    /**
     * Add a tab menu item.
     *
     * @param string|Tab $name     name of tab or Tab object
     * @param array      $settings Tab settings
     *
     * @return Tab|View tab menu item view
     */
    protected function addTabMenuItem($name, $settings)
    {
        if (is_object($name)) {
            $tab = $name;
        } else {
            $tab = new Tab($name);
        }

        $tab = $this->add(Factory::mergeSeeds(['class' => ['item'], 'settings' => $settings], $tab), 'Menu')
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
     * @param string $name name of view
     *
     * @return TabsSubview
     */
    protected function addSubView($name)
    {
        return TabsSubview::addTo($this, ['dataTabName' => $name], ['Tabs']);
    }

    protected function renderView(): void
    {
        // use content as class name
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
