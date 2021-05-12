<?php

declare(strict_types=1);
/**
 * Interface for a Layout using a navigable side menu.
 */

namespace Atk4\Ui\Layout;

use Atk4\Ui\Item;
use Atk4\Ui\Menu;

interface NavigableInterface
{
    /**
     * Add a group to left menu.
     */
    public function addMenuGroup($seed): Menu;

    /**
     * Add items to left menu.
     * Will place item in a group if supply.
     */
    public function addMenuItem($name, $action = null, $group = null): Item;
}
