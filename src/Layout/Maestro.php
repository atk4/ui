<?php
/**
 * An Admin layout with enhance left menu.
 */

namespace atk4\ui\Layout;

use atk4\ui\Menu;

class Maestro extends Admin
{
    public $menuTemplate = 'layout/maestro-left-menu.html';

    public function addLeftMenuGroup($seed): Menu
    {
        $gr = $this->menuLeft->addGroup($seed, $this->menuTemplate)->addClass('atk-maestro-left-menu-group');
        $gr->removeClass('item');

        return $gr;
    }

    public function renderView()
    {
        parent::renderView();

        $js = (new \atk4\ui\jQuery('.atk-maestro-left-menu-group'))->atkAdminMenu(['base' => $_SERVER['REQUEST_URI']]);

        $this->js(true, $js);
    }
}
