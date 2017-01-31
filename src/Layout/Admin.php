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
 *  - LeftMenu  (has_leftMenu)
 *  - Menu
 *  - RightMenu (has_righMenu)
 *  - UserCard
 *  - BreadCrumb
 *  - Footer
 *
 *  - Content
 */
class Admin extends Generic
{
    public $leftMenu = null;    // vertical menu
    public $menu = null;        // horizontal menu
    public $rightMenu = null;   // vertical pull-down

    public $userCard = null;    // for currently-logged-in-user

    public $breadCrumb = null;  // for Breadcrumb

    public $defaultTemplate = 'layout/admin.html';

    public function init()
    {
        parent::init();

        if ($this->menu === null) {
            $this->menu = $this->add(new Menu('inverted fixed horizontal'), 'TopMenu');
        }

        if ($this->rightMenu === null) {
            $this->rightMenu = $this->menu->add(new Menu(['ui'=>false]), 'RightMenu')
                ->addClass('right menu')->removeClass('item');
        }

        if ($this->leftMenu === null) {
            $this->leftMenu = $this->add(new Menu('left vertical inverted labeled visible sidebar'), 'LeftMenu');
            $this->leftMenu->addHeader($this->app->title);
        }
    }

    public function renderView()
    {
        if ($this->leftMenu) {
            if (count($this->leftMenu->elements) == 1) {
                // no items were added, so lets add dashboard
                $this->leftMenu->addItem(['Dashboard', 'icon'=>'dashboard'], 'index');
            }
            $this->leftMenu->addItem(['Logout', 'icon'=>'sign out'], 'logout');
        }
        parent::renderView();
    }
}
