<?php

namespace atk4\ui\Layout;

use atk4\ui\jQuery;
use atk4\ui\Menu;
use atk4\ui\Template;

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
class Admin extends Generic
{
    public $menuLeft = null;    // vertical menu
    public $menu = null;        // horizontal menu
    public $menuRight = null;   // vertical pull-down

    public $burger = true;      // burger menu item

    /*
     * Whether or not left Menu is visible on Page load.
     */
    public $isMenuLeftVisible = true;

    /**
     * Obsolete, use menuLeft.
     *
     * @obsolete
     */
    public $leftMenu = null;

    public $defaultTemplate = 'layout/admin.html';

    public function init(): void
    {
        parent::init();

        if ($this->menu === null) {
            $this->menu = Menu::addTo($this, ['atk-topMenu inverted fixed horizontal', 'element' => 'header'], ['TopMenu']);
            $this->burger = $this->menu->addItem(['class' => ['icon atk-leftMenuTrigger']]);
            $this->burger->on('click', [
                (new jQuery('.ui.left.sidebar'))->toggleClass('visible'),
                (new jQuery('body'))->toggleClass('atk-leftMenu-visible'),
            ]);
            \atk4\ui\Icon::addTo($this->burger, ['content']);
        }

        if ($this->menuRight === null) {
            $this->menuRight = Menu::addTo($this->menu, ['ui' => false], ['RightMenu'])
                ->addClass('right menu')->removeClass('item');
        }

        if ($this->menuLeft === null) {
            $this->menuLeft = Menu::addTo($this, ['left vertical inverted labeled sidebar'], ['LeftMenu']);
            $this->leftMenu = $this->menuLeft;

            $closeIcon = \atk4\ui\View::addTo($this->menuLeft, ['template' => new Template('<a id="{$_id}" href="#" onclick="return false;" class="{$class} item atk-leftMenuClose"><i class="close icon"></i></a>')]);
            $closeIcon->on('click', (new jQuery('body'))->removeClass('atk-leftMenu-visible'));

            $this->menuLeft->addHeader($this->app->title);
        }

        $this->template->trySet('version', $this->app->version);
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->menuLeft) {
            if (count($this->menuLeft->elements) == 1) {
                // no items were added, so lets add dashboard
                $this->menuLeft->addItem(['Dashboard', 'icon' => 'dashboard'], 'index');
            }
            if ($this->isMenuLeftVisible) {
                $this->menuLeft->addClass('visible');
            }
            //$this->leftMenu->addItem(['Logout', 'icon'=>'sign out'], ['logout']);
        }
        parent::renderView();
    }
}
