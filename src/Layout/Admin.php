<?php

declare(strict_types=1);

namespace Atk4\Ui\Layout;

use Atk4\Ui\Header;
use Atk4\Ui\Icon;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Layout;
use Atk4\Ui\Menu;
use Atk4\Ui\MenuItem;

/**
 * Implements a classic 100% width admin layout.
 *
 * Optional left menu in inverse with fixed width is most suitable for contextual navigation or
 *  providing core object list (e.g. folders in mail)
 *
 * Another menu on the top for actions that can have a pull-down menus.
 *
 * A top-right spot is for user icon or personal menu, labels or stats.
 *
 * On top of the content there is automated title showing page title but can also work as a bread-crumb or container for buttons.
 *
 * Footer for a short copyright notice and perhaps some debug elements.
 *
 * Spots:
 *  - LeftMenu  (has_menuLeft)
 *  - Menu
 *  - RightMenu (has_menuRight)
 *  - Footer
 *
 *  - Content
 */
class Admin extends Layout implements NavigableInterface
{
    /** @var Menu Top horizontal menu */
    public $menu;
    /** @var Menu|null Left vertical menu */
    public $menuLeft;
    /** @var Menu Right vertical menu pull-down */
    public $menuRight;

    /** @var MenuItem */
    public $burger;

    /** @var bool Whether or not left Menu is visible on Page load. */
    public $isMenuLeftVisible = true;

    public $defaultTemplate = 'layout/admin.html';

    protected function init(): void
    {
        parent::init();

        if ($this->menu === null) {
            $this->menu = Menu::addTo(
                $this,
                ['inverted fixed horizontal atk-admin-top-menu', 'element' => 'header'],
                ['TopMenu']
            );
            $this->burger = $this->menu->addItem(['class' => ['icon']]);
            $this->burger->on('click', new JsBlock([
                (new Jquery('.atk-sidenav'))->toggleClass('visible'),
                (new Jquery('body'))->toggleClass('atk-sidenav-visible'),
            ]));
            Icon::addTo($this->burger, ['content']);

            Header::addTo($this->menu, [$this->getApp()->title, 'size' => 4]);
        }

        if ($this->menuRight === null) {
            $this->menuRight = Menu::addTo($this->menu, ['ui' => false], ['RightMenu'])
                ->addClass('right menu')->removeClass('item');
        }

        if ($this->menuLeft === null) {
            $this->menuLeft = Menu::addTo($this, ['ui' => 'atk-sidenav-content'], ['LeftMenu']);
        }

        $this->template->trySet('version', $this->getApp()->version);
    }

    public function addMenuGroup($seed): Menu
    {
        return $this->menuLeft->addGroup($seed);
    }

    public function addMenuItem($name, $action = null, $group = null): MenuItem
    {
        if ($group) {
            return $group->addItem($name, $action);
        }

        return $this->menuLeft->addItem($name, $action);
    }

    protected function renderView(): void
    {
        if (count($this->menuLeft->elements) === 0) {
            // no items were added, so lets add dashboard
            $this->menuLeft->addItem(['Dashboard', 'icon' => 'dashboard'], ['index']);
        }
        if (!$this->isMenuLeftVisible) {
            $this->template->tryDel('CssVisibility');
        }

        parent::renderView();
    }
}
