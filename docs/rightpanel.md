:::{php:namespace} Atk4\Ui
:::

(rightpanel)=

# Right Panel

:::{php:class} Panel\Right
:::

Right panel are view attached to the app layout. They are display on demand via javascript event
and can display content statically or dynamically using Loadable Content.

Demo: https://ui.atk4.org/demos/layout/layout-panel.php

## Basic Usage

Adding a right panel to the app layout and adding content to it:

```
$panel = $app->layout->addRightPanel(new \Atk4\Ui\Panel\Right(['dynamic' => false]));
Message::addTo($panel, ['This panel contains only static content.']);
```

By default, panel content are loaded dynamically. If you want to only add static content, you need to specify
the {ref}`dynamic` property and set it to false.

Opening of the panel is done via a javascript event. Here, we simply register a click event on a button that will open
the panel:

```
$button = Button::addTo($app, ['Open Static']);
$button->on('click', $panel->jsOpen());
```

### Loading content dynamically

Loading dynamic content within panel is done via the onOpen method

:::{php:method} onOpen($callback)
:::

Initializing a panel with onOpen callback:

```
$panel = $app->layout->addRightPanel(new \Atk4\Ui\Panel\Right());
Message::addTo($panel, ['This panel will load content dynamically below according to button select on the right.']);
$button = Button::addTo($app, ['Button 1']);
$button->js(true)->data('btn', '1');
$button->on('click', $panel->jsOpen(['btn'], 'orange'));

$panel->onOpen(function (Panel\Content $p) {
    $buttonNumber = $p->getApp()->tryGetRequestQueryParam('btn');
    $text = 'You loaded panel content using button #' . $buttonNumber;
    Message::addTo($p, ['Panel 1', 'text' => $text]);
});
```

:::{php:method} jsOpen
This method may take up to three arguments.

```{eval-rst}
:param $args: an array of data property to carry with the callback URL. Let's say that you triggering element as a data property name ID (data-id) then if specify, the data ID value will be sent as a get argument with the callback URL.
:param $activeCss: a string representing the active state of the triggering element. This CSS class will be applied to the trigger element as long as the panel remains open. This help visualize, which element has trigger the panel opening.
:param $jsTrigger: JS expression that represent the jQuery object where the data property reside. Default to $(this).
```
:::
