<?php

declare(strict_types=1);

namespace Atk4\Ui\Layout;

use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Menu;
use Atk4\Ui\MenuItem;

/**
 * An Admin layout with enhanced left menu.
 * This layout use jQuery plugin atk-sidenav.plugin.js
 *  Default value for this plugin is set for Maestro layout using maestro-sidenav.html template.
 *  Note that it is possible to change these default value if another template is use.
 */
class Maestro extends Admin
{
    /** @var string */
    public $menuTemplate = 'layout/maestro-sidenav.html';

    public function addMenuGroup($seed): Menu
    {
        $gr = $this->menuLeft->addGroup($seed, $this->menuTemplate)->addClass('atk-maestro-sidenav');
        $gr->removeClass('item');

        return $gr;
    }

    public function addMenuItem($name, $action = null, $group = null): MenuItem
    {
        $i = parent::addMenuItem($name, $action, $group);
        if (!$group) {
            $i->addClass('atk-maestro-sidenav');
        }

        return $i;
    }

    protected function renderView(): void
    {
        parent::renderView();

        // initialize all menu group at once
        // since atkSideNav plugin default setting are for Maestro, no need to pass settings to initialize it
        $js = (new Jquery('.atk-maestro-sidenav'))->atkSidenav();

        $this->js(true, $js);
    }
}
