<?php
/**
 * Interface for a Layout using a navigable side menu.
 */

namespace atk4\ui\Layout;

use atk4\ui\Item;
use atk4\ui\Menu;

interface Navigable
{
    /**
     * Add a group to left menu.
     *
     * @param $seed
     */
    public function addMenuGroup($seed): Menu;

    /**
     * Add items to left menu.
     *  Will place item in a group if supply.
     *
     * @param $name
     * @param null $action
     * @param null $group
     */
    public function addMenuItem($name, $action = null, $group = null): Item;
}
