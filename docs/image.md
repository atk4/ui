:::{php:namespace} Atk4\Ui
:::

(image)=

# Image

:::{php:class} Image
:::

Implements Image around https://fomantic-ui.com/elements/image.html.

## Basic Usage

Implements basic image:

```
$icon = Image::addTo($app, ['image.gif']);
```

You need to make sure that argument specified to Image is a valid URL to an image.

## Specify classes

You can pass additional classes to an image:

```
$img = $app->cdn['atk'] . '/logo.png';
$icon = Image::addTo($app, [$img, 'class.disabled' => true]);
```
