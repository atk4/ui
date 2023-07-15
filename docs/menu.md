.. _menu:

# Menu

:::{php:namespace} Atk4\Ui
:::

:::{php:class} Menu
:::

Menu implements horizontal or vertical multi-level menu by using Fomantic-UI 'menu'.

## Using Menu

.. php:method: addItem($label, $action)

Here is a simple usage:

```
$menu = Menu::addTo($app);
$menu->addItem('foo');
$menu->addItem('bar');
```

to make menu vertical:

```
$menu->addClass('vertical');
```

## Decorating Menu Items

See :php:class:`MenuItem` for more options:

```
$menu->addItem(['foo', 'icon' => 'book']);
```

## Specifying Links and Actions

Menu items can use links and actions:

```
$menu->addItem('foo', 'test.php');
$menu->addItem('bar', new JsModal('Test'));
```

## Creating sub-menus

.. php:method: addMenu($label)
.. php:method: addGroup($label)
.. php:method: addRightMenu($label)

You can create sub-menu for either vertical or horizontal menu. For a vertical
menu you can also use groups. For horizontal menu, you can use addRightMenu.

:

```
$menu = Menu::addTo($app);
$menu->addItem('foo');
$sub = $menu->addMenu('Some Bars');
$sub->addItem('bar 1');
$sub->addItem('bar 2');
```

## Headers

.. php:method: addHeader($label)

## Advanced Use

You can add other elements inside menu. Refer to demos/menu.php.

## MenuItem

:::{php:class} MenuItem
:::

:::{php:attr} label
:::

:::{php:attr} $icon
:::

Additionally you can use :php:meth:`View::addClass()` to disable or style your menu items.
