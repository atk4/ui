
.. _popup:

=====
Popup
=====

.. php:namespace:: atk4\ui

.. php:class:: Popup

Implements a popup::

    $button = Button::addTo($app, ['Click me']);
    HelloWorld::addTo(Popup::addTo($app, [$button]));

.. php:method:: set($callback)

Popup can also operate with dynamic content::

    $button = Button::addTo($app, ['Click me']);
    Popup::addTo($app, [$button])
        ->set('hello world with rand='.rand(1,100));

Pop-up should be added into a viewport which will define boundaries of a pop-up, but it will
be positioned relative to the $button. Popup remains invisible until it's triggered by event of $button.

If second argument in the :ref:`seed` is of class :php:class:`Button`, :php:class:`Menu`,
:php:class:`Item` or :php:class:`DropDown` (note - NOT FormField!), pop-up will also bind itself
to that element. The above example will automatically bind "click" event of a button to open a pop-up.

When added into a menu, pop-up will appear on hover::

    $m = Menu::addTo($app);
    $item = $m->addItem('HoverMe')
    Text::addTo(Popup::addTo($app, [$item]))->set('Appears when you hover a menu item');

Like many other Views of ATK, popup is an interractive element. It can load it's contents when opened::

    $m = Menu::addTo($app);
    $item = $m->addItem('HoverMe');
    Popup::addTo($app, [$item])->set(function($popup) {
        Text::addTo($popup)->set('Appears when you hover a menu item');
        Label::addTo($popup, ['Random value', 'detail'=>rand(1,100)]);
    });

Demo: https://ui.agiletoolkit.org/demos/popup.php

Fomantic UI: https://fomantic-ui.com/modules/popup.html

