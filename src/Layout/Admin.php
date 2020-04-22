<?php

namespace atk4\ui\Layout;

use atk4\ui\Exception;
use atk4\ui\Header;
use atk4\ui\Icon;
use atk4\ui\Item;
use atk4\ui\jQuery;
use atk4\ui\Menu;

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
class Admin extends Generic implements Navigable
{
    public $menuLeft = null;    // vertical menu
    public $menu = null;        // horizontal menu
    public $menuRight = null;   // vertical pull-down

    public $burger = true;      // burger menu item

    /** @var bool Whether or not left Menu is visible on Page load. */
    public $isMenuLeftVisible = true;

    public $defaultTemplate = 'layout/admin.html';

    public function init(): void
    {
        parent::init();

        if ($this->menu === null) {
            $this->menu = Menu::addTo($this, ['inverted fixed horizontal', 'element' => 'header'], ['TopMenu']);
            $this->burger = $this->menu->addItem(['class' => ['icon']]);
            $this->burger->on('click', [
                (new jQuery('.atk-sidenav'))->toggleClass('visible'),
                (new jQuery('body'))->toggleClass('atk-sidenav-visible'),
            ]);
            Icon::addTo($this->burger, ['content']);

            Header::addTo($this->menu, [$this->app->title, 'size' => 4]);
        }

        if ($this->menuRight === null) {
            $this->menuRight = Menu::addTo($this->menu, ['ui' => false], ['RightMenu'])
                                   ->addClass('right menu')->removeClass('item');
        }

        if ($this->menuLeft === null) {
            $this->menuLeft = Menu::addTo($this, ['ui' => 'atk-sidenav-content'], ['LeftMenu']);
        }

        $this->template->trySet('version', $this->app->version);
    }

    /**
     * Add a group to left menu.
     *
     * @param $seed
     *
     * @return Menu
     */
    public function addMenuGroup($seed): Menu
    {
        return $this->menuLeft->addGroup($seed);
    }

    /**
     * Add items to left menu.
     *
     * @param $name
     * @param null $action
     * @param null $group
     *
     * @return Item
     */
    public function addMenuItem($name, $action = null, $group = null): Item
    {
        if ($group) {
            $i = $group->addItem($name, $action);
        } else {
            $i = $this->menuLeft->addItem($name, $action);
        }

        return $i;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->menuLeft) {
            if (count($this->menuLeft->elements) === 0) {
                // no items were added, so lets add dashboard
                $this->menuLeft->addItem(['Dashboard', 'icon' => 'dashboard'], ['index']);
            }
            if ($this->isMenuLeftVisible) {
                $this->menuLeft->addClass('visible');
            }
        }

        parent::renderView();
    }
}
