<?php

namespace atk4\ui\Layout;

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

    public $template = 'layout/admin.html';

    function init() {
    }

}
