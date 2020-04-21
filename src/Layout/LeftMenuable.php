<?php
/**
 * Interface for a Layout using a left menu.
 */

namespace atk4\ui\Layout;

use atk4\ui\Item;
use atk4\ui\Menu;

interface LeftMenuable
{
    /**
     * Add a group to left menu.
     *
     * @param $seed
     *
     * @return Menu
     */
    public function addLeftMenuGroup($seed): Menu;

    /**
     * Add items to left menu.
     *  Will place item in a group if supply.
     *
     * @param $name
     * @param null $action
     * @param null $group
     *
     * @return Item
     */
    public function addLeftMenuItem($name, $action = null, $group = null): Item;
}
