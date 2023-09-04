:::{php:namespace} Atk4\Ui
:::

(popup)=

# Popup

:::{php:class} Popup
:::

Implements a popup:

```
$button = Button::addTo($app, ['Click me']);
HelloWorld::addTo(Popup::addTo($app, [$button]));
```

:::{php:method} set($callback)
:::

Popup can also operate with dynamic content:

```
$button = Button::addTo($app, ['Click me']);
Popup::addTo($app, [$button])
    ->set('hello world with rand=' . rand(1, 100));
```

Pop-up should be added into a viewport which will define boundaries of a pop-up, but it will
be positioned relative to the $button. Popup remains invisible until it's triggered by event of $button.

If second argument in the {ref}`seed` is of class {php:class}`Button`, {php:class}`Menu`,
{php:class}`MenuItem` or {php:class}`Dropdown` (note - NOT {php:class}`Form\Control`!), pop-up will also bind itself
to that element. The above example will automatically bind "click" event of a button to open a pop-up.

When added into a menu, pop-up will appear on hover:

```
$menu = Menu::addTo($app);
$item = $menu->addItem('HoverMe')
Text::addTo(Popup::addTo($app, [$item]))->set('Appears when you hover a menu item');
```

Like many other Views of ATK, popup is an interactive element. It can load it's contents when opened:

```
$menu = Menu::addTo($app);
$item = $menu->addItem('HoverMe');
Popup::addTo($app, [$item])->set(function (View $p) {
    Text::addTo($p)->set('Appears when you hover a menu item');
    Label::addTo($p, ['Random value', 'detail' => rand(1, 100)]);
});
```

Demo: https://ui.atk4.org/demos/popup.php

Fomantic-UI: https://fomantic-ui.com/modules/popup.html
