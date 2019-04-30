.. php:namespace:: atk4\ui

======
Header
======

Can be used for page or section headers.

Based around: https://fomantic-ui.com/elements/header.html.

Demo:  https://ui.agiletoolkit.org/demos/header.php

Basic Usage
===========

By default header size will depend on where you add it::

    $this->add(['Header', 'Hello, Header']);

Attributes
==========

.. php:attr:: size

.. php:attr:: subHeader

Specify size and sub-header content::

    $seg->add([
        'Header',
        'H1 header',
        'size'=>1,
        'subHeader'=>'H1 subheader'
    ]);

    // or

    $seg->add([
        'Header',
        'Small header',
        'size'=>'small',
        'subHeader'=>'small subheader'
    ]);

Icon and Image
===============

.. php:attr:: icon

.. php:attr:: image


Header may specify icon or image::

    $seg->add([
        'Header',
        'Header with icon',
        'icon'=>'settings',
        'subHeader'=>'and with sub-header'
    ]);

Here you can also specify seed for the image::

    $img = 'https://cdn.rawgit.com/atk4/ui/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';
    $seg->add([
        'Header',
        'Center-aligned header',
        'aligned'=>'center',
        'image'=>[$img, 'disabled'],
        'subHeader'=>'header with image'
    ]);

