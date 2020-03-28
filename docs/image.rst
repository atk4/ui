
.. _image:

=====
Image
=====

.. php:namespace:: atk4\ui

.. php:class:: Image

Implements Image around https://fomantic-ui.com/elements/image.html.

Basic Usage
===========

Implements basic image::

    $icon = Image::addTo($app, ['image.gif']);

You need to make sure that argumen specified to Image is a valid URL to an image.

Specify classes
===============

You can pass additional classes to an image::

    $img = 'https://raw.githubusercontent.com/atk4/ui/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';
    $icon = Image::addTo($app, [$img, 'disabled']);

