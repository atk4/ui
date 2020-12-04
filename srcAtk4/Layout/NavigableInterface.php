<?php

declare(strict_types=1);
/**
 * Interface for a Layout using a navigable side menu.
 */

namespace Atk4\Ui\Layout;

use atk4\ui\Item;
use atk4\ui\Menu;

interface NavigableInterface
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
     */
    public function addMenuItem($name, $action = null, $group = null): Item;
}
