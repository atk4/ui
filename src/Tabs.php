<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;

class Tabs extends ViewWithContent
{
    public $defaultTemplate = 'tabs.html';
    public $ui = 'tabbed menu';

    /** @var string name of active tab */
    public $activeTabName;

    /**
     * @param string|TabsTab                                                                                    $name
     * @param \Closure(VirtualPage, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): void $callback
     * @param array<string, mixed>                                                                              $settings
     *
     * @return View
     */
    public function addTab($name, ?\Closure $callback = null, array $settings = [])
    {
        $item = $this->addTabMenuItem($name, $settings);
        $sub = $this->addSubView($item->name);

        // if there is callback action, then use VirtualPage
        if ($callback) {
            $vp = VirtualPage::addTo($sub, ['ui' => '']);

            // TODO hack like https://github.com/atk4/ui/blob/5.2.0/src/AccordionSection.php#L37
            View::addTo($sub, ['name' => $vp->name]);

            $reloadViewArgs = ['url' => $vp->getJsUrl('cut')];
            if (isset($item->settings['apiSettings'])) {
                $reloadViewArgs['apiConfig'] = $item->settings['apiSettings'];
            }
            unset($item->settings['apiSettings']);

            if (($item->settings['cache'] ?? null) === false) {
                $item->settings['onLoad'] = new JsFunction([], [$vp->js()->atkReloadView($reloadViewArgs)]);
            } else {
                // Fomantic-UI "onFirstLoad" callback is not used as it does not allow to reload the tab when the 1st load failed
                $reloadViewArgs['apiConfig']['onComplete'] = new JsFunction(['response'], [
                    new JsExpression('if (!response || !response.success) { [] }', [new JsBlock([$sub->js()->removeData('atkTabLoaded')])]),
                ]);
                $item->settings['onLoad'] = new JsFunction([], [
                    new JsExpression('if (![]) { [] }', [
                        $sub->js()->data('atkTabLoaded'),
                        new JsBlock([
                            $sub->js()->data('atkTabLoaded', true),
                            $vp->js()->atkReloadView($reloadViewArgs),
                        ]),
                    ]),
                ]);
            }
            unset($item->settings['cache']);

            $vp->set($callback);
        }

        return $sub;
    }

    /**
     * Adds dynamic tab in tabs widget which will load a separate
     * page/url when activated.
     *
     * @param string|TabsTab                           $name
     * @param string|array<0|string, string|int|false> $page     URL to open inside a tab
     * @param array<string, mixed>                     $settings
     */
    public function addTabUrl($name, $page, array $settings = []): void
    {
        $item = $this->addTabMenuItem($name, $settings);
        $this->addSubView($item->name);

        $item->setUrl($page);
    }

    /**
     * Add a tab menu item.
     *
     * @param string|TabsTab       $name
     * @param array<string, mixed> $settings
     *
     * @return TabsTab
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

    #[\Override]
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
