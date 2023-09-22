:::{php:namespace} Atk4\Ui
:::

:::{php:class} ProgressBar
:::

# ProgressBar

ProgressBar is actually a quite simple element, but it can be made quite interactive along with
{php:class}`JsSse`.

Demo: https://ui.atk4.org/demos/progressbar.php

## Basic Usage

:::{php:method} jsValue($value)
:::

After adding a console to your {ref}`render_tree`, you just need to set a callback:

```
// add progressbar showing 0 (out of 100)
$bar = ProgressBar::addTo($app);

// add with some other value of 20% and label
$bar2 = ProgressBar::addTo($app, [20, '% Battery']);
```

The value of the progress bar can be changed either before rendering, inside PHP, or after rendering
with JavaScript:

```
$bar->value = 5; // sets this value instead of 0

Button::addTo($app, ['charge up the battery'])
    ->on('click', $bar2->jsValue(100));
```

## Updating Progress in RealTime

You can use real-time element such as JsSse or Console (which relies on JsSse) to execute
jsValue() of your progress bar and adjust the display value.

Demo: https://ui.atk4.org/demos/progressbar.php

{php:class}`Console` also implements method {php:meth}`Console::send` so you can use it to send progress
updates of your progress-bar.
