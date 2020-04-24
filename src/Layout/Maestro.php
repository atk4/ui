<?php
/**
 * An Admin layout with enhance left menu.
 * This layout use jQuery plugin atk-sidenav.plugin.js
 *  Default value for this plugin is set for Maestro layout using maestro-sidenav.html template.
 *  Note that it is possible to change these default value if another template is use.
 *
 */

namespace atk4\ui\Layout;

use atk4\ui\jQuery;
use atk4\ui\Item;
use atk4\ui\Menu;

class Maestro extends Admin
{
    public $menuTemplate = 'layout/maestro-sidenav.html';

    public function addMenuGroup($seed): Menu
    {
        $gr = $this->menuLeft->addGroup($seed, $this->menuTemplate)->addClass('atk-maestro-sidenav');
        $gr->removeClass('item');

        return $gr;
    }

    public function addMenuItem($name, $action = null, $group = null): Item
    {
        $i = parent::addMenuItem($name, $action, $group);
        if (!$group) {
            $i->addClass('atk-maestro-sidenav');
        }

        return $i;
    }

    public function renderView()
    {
        parent::renderView();

        //initialize all menu group at ounce.
        //since atkSideNav plugin default setting are for Maestro, no need to pass settings to initialize it.
        $js = (new jQuery('.atk-maestro-sidenav'))->atkSidenav();

        $this->js(true, $js);
    }
}
