:::{php:namespace} Atk4\Ui
:::

:::{php:class} Accordion
:::

# Accordion

Accordion implement another way to organize your data. The implementation is based on: https://fomantic-ui.com/modules/accordion.html.

Demo: https://ui.atk4.org/demos/accordion.php

## Basic Usage

Once you create an Accordion container you can then mix and match static and dynamic accodion section:

```
$acc = Accordion::addTo($app);
```

Adding a static content section is pretty simple:

```
LoremIpsum::addTo($acc->addSection('Static Tab'));
```

You can add multiple elements into a single accordion section, like any other view.

:::{php:method} addSection($name, $action = null, $icon = 'dropdown')
:::

Use addSection() method to add more section in an Accordion view. First parameter is a title of the section.

Section can be static or dynamic. Dynamic sections use {php:class}`VirtualPage` implementation mentioned above.
You should pass Closure action as a second parameter.

Example:

```
$t = Accordion::addTo($layout);

// add static section
HelloWorld::addTo($t->addSection('Static Content'));

// add dynamic section
$t->addSection('Dynamically Loading', function (VirtualPage $vp) {
    LoremIpsum::addTo($vp);
});
```

## Dynamic Accordion Section

Dynamic sections are based around implementation of {php:class}`VirtualPage` and allow you
to pass a callback which will be triggered when user clicks on the section title.:

```
$acc = Accordion::addTo($app);

// dynamic section
$acc->addSection('Dynamic Lorem Ipsum', function (VirtualPage $vp) {
    LoremIpsum::addTo($vp, ['size' => 2]);
});
```

## Controlling Accordion Section via Javascript

Accordion class has some wrapper method in order to control the accordion module behavior.

:::{php:method} jsOpen($section, $action = null)
:::

:::{php:method} jsToggle($section, $action = null)
:::

:::{php:method} jsClose($section, $action = null)
:::

For example, you can set a button that, when clicked, will toggle an accordion section:

```
$button = Button::addTo($bar, ['Toggle Section 1']);

$acc = Accordion::addTo($app, ['type' => ['styled', 'fluid']]);
$section1 = LoremIpsum::addTo($acc->addSection('Static Text'));
$section2 = LoremIpsum::addTo($acc->addSection('Static Text'));

$button->on('click', $acc->jsToggle($section1));
```

## Accordion Module settings

It is possible to change Accordion module settings via the settings property.:

```
Accordion::addTo($app, ['settings' => []]);
```

For a complete list of all settings for the Accordion module, please visit: https://fomantic-ui.com/modules/accordion.html#/settings
