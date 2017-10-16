<?php

namespace atk4\ui\Layout;

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
class Admin extends Generic
{
    public $menuLeft = null;    // vertical menu
    public $menu = null;        // horizontal menu
    public $menuRight = null;   // vertical pull-down

    public $burger = true;      // burger menu item

    /**
     * Obsolete, use menuLeft.
     *
     * @obsolete
     */
    public $leftMenu = null;

    public $defaultTemplate = 'layout/admin.html';

    public function init()
    {
        parent::init();

        if ($this->menu === null) {
            $this->menu = $this->add(['Menu', 'atk-topMenu inverted fixed horizontal', 'element'=>'header'], 'TopMenu');
            $this->burger = $this->menu->addItem(['class'=>['icon atk-leftMenuTrigger']])->add(['Icon', 'content']);
        }

        if ($this->menuRight === null) {
            $this->menuRight = $this->menu->add(new Menu(['ui'=>false]), 'RightMenu')
                ->addClass('right menu')->removeClass('item');
        }

        if ($this->menuLeft === null) {
            $this->menuLeft = $this->add(new Menu('left vertical inverted labeled visible sidebar'), 'LeftMenu');
            $this->leftMenu = $this->menuLeft;
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
                $this->menuLeft->addItem(['Dashboard', 'icon'=>'dashboard'], 'index');
            }
            //$this->leftMenu->addItem(['Logout', 'icon'=>'sign out'], ['logout']);
        }
        parent::renderView();
    }
}
