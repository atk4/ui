<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;

class Tabs extends View
{
    public $defaultTemplate = 'tabs.html';
    public $ui = 'tabular menu';

    /** @var string name of active tab */
    public $activeTabName;

    /**
     * @param string|TabsTab                                                                                    $name
     * @param \Closure(VirtualPage, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): void $callback
     *
     * @return View
     */
    public function addTab($name, \Closure $callback = null, array $settings = [])
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
     * @param string|TabsTab                           $name
     * @param string|array<0|string, string|int|false> $page URL to open inside a tab
     */
    public function addTabUrl($name, $page, array $settings = []): void
    {
        $item = $this->addTabMenuItem($name, $settings);
        $this->addSubView($item->name);

        $item->setPath($page);
    }

    /**
     * Add a tab menu item.
     *
     * @param string|TabsTab $name
     *
     * @return TabsTab|View tab menu item view
     */
    protected function addTabMenuItem($name, array $settings)
    {
        if (is_object($name)) {
            $tab = $name;
        } else {
            $tab = new TabsTab($name);
        }

        $tab = $this->add(Factory::mergeSeeds(['class' => ['item'], 'settings' => $settings], $tab), 'Menu')
            ->setAttr('data-tab', $tab->name);

        if (!$this->activeTabName) {
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
