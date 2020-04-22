<?php
/**
 * An Admin layout with enhance left menu.
 */

namespace atk4\ui\Layout;

use atk4\ui\Item;
use atk4\ui\Menu;

class Maestro extends Admin
{
    public $menuTemplate = 'layout/maestro-left-menu.html';

    public function addMenuGroup($seed): Menu
    {
        $gr = $this->menuLeft->addGroup($seed, $this->menuTemplate)->addClass('atk-maestro-left-menu-group');
        $gr->removeClass('item');

        return $gr;
    }

    public function addMenuItem($name, $action = null, $group = null): Item
    {
        $i = parent::addMenuItem($name, $action, $group);
        if (!$group) {
            $i->addClass('atk-maestro-left-menu-group');
        }

        return $i;
    }

    public function renderView()
    {
        parent::renderView();

        //initialize all menu group at ounce.
        $js = (new \atk4\ui\jQuery('.atk-maestro-left-menu-group'))->atkAdminMenu();

        $this->js(true, $js);
    }
}
