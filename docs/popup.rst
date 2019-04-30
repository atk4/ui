
.. _popup:

=====
Popup
=====

.. php:namespace:: atk4\ui

.. php:class:: Popup

Implements a popup::

    $button = $app->add(['Button', 'Click me']);
    $app->add(['Popup', $button])->add('HelloWorld');

.. php:method:: set($callback)

Popup can also operate with dynamic content::

    $button = $app->add(['Button', 'Click me']);
    $app->add(['Popup', $button])
        ->set('hello world with rand='.rand(1,100));

Pop-up should be added into a viewport which will define boundaries of a pop-up, but it will
be positioned relative to the $button. Popup remains invisible until it's triggered by event of $button.

If second argument in the :ref:`seed` is of class :php:class:`Button`, :php:class:`Menu`,
:php:class:`Item` or :php:class:`DropDown` (note - NOT FormField!), pop-up will also bind itself
to that element. The above example will automatically bind "click" event of a button to open a pop-up.

When added into a menu, pop-up will appear on hover::

    $m = $app->add('Menu');
    $item = $m->addItem('HoverMe')
    $app->add(['Popup', $item])
        ->add('Text')->set('Appears when you hover a menu item');

Like many other Views of ATK, popup is an interractive element. It can load it's contents when opened::

    $m = $app->add('Menu');
    $item = $m->addItem('HoverMe');
    $app->add(['Popup', $item])->set(function($popup) {
        $popup->add('Text')->set('Appears when you hover a menu item');
        $popup->add(['Label', 'Random value', 'detail'=>rand(1,100)]);
    });

Demo: https://ui.agiletoolkit.org/demos/popup.php

Fomantic UI: https://fomantic-ui.com/modules/popup.html

