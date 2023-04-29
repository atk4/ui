<?php

declare(strict_types=1);

namespace Atk4\Ui\Layout;

use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Menu;
use Atk4\Ui\MenuItem;

interface NavigableInterface
{
    /**
     * Add a group to left menu.
     *
     * @param array $seed
     */
    public function addMenuGroup($seed): Menu;

    /**
     * Add items to left menu.
     *
     * Will place item in a group if supply.
     *
     * @param string|array                  $name
     * @param string|array|JsExpressionable $action
     * @param Menu                          $group
     */
    public function addMenuItem($name, $action = null, $group = null): MenuItem;
}
