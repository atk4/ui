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

    Header::addTo($this, ['Hello, Header']);

Attributes
==========

.. php:attr:: size

.. php:attr:: subHeader

Specify size and sub-header content::

    Header::addTo($seg, [
        'H1 header',
        'size'=>1,
        'subHeader'=>'H1 subheader'
    ]);

    // or

    Header::addTo($seg, [
        'Small header',
        'size'=>'small',
        'subHeader'=>'small subheader'
    ]);

Icon and Image
===============

.. php:attr:: icon

.. php:attr:: image


Header may specify icon or image::

    Header::addTo($seg, [
        'Header with icon',
        'icon'=>'settings',
        'subHeader'=>'and with sub-header'
    ]);

Here you can also specify seed for the image::

    $img = 'https://raw.githubusercontent.com/atk4/ui/2.0.4/public/logo.png';
    Header::addTo($seg, [
        'Center-aligned header',
        'aligned'=>'center',
        'image'=>[$img, 'disabled'],
        'subHeader'=>'header with image'
    ]);

