
.. _popup:

======
Popup
======

.. php:namespace:: atk4\ui

.. php:class:: Popup

Implements a popup::

    $button = $app->add(['Button', 'Click me']);
    $button->add('Popup')->add('Text')
        ->set('hello world from inside a popup');

When pop-up is placed inside :php:class:`Button`, :php:class:`Menu`, :php:class:`Item` or :php:class:`DropDown`
(note - NOT FormField!), pop-up will also bind itself to the parent element. The above example will
automatically bind "click" event of a button to open a pop-up.

When added into a menu, pop-up will appear on hover::

    $m = $app->add('Menu');
    $m->addItem('HoverMe')->add('Popup')
        ->add('Text')->set('Appears when you hover a menu item');

Like many other Views of ATK, popup is an interractive element. It can load it's contents when opened::

    $m = $app->add('Menu');
    $m->addItem('HoverMe')->add('Popup')->set(function($popup) {
        $popup->add('Text')->set('Appears when you hover a menu item');
        $popup->add(['Label', 'Random value', 'detail'=>rand(1,100)]);
    });

.. important:: Although adding PopUp inside a view like this is quick and efficient, this also places
    popup inside menu's DOM view. As a result, this may have some effect on CSS. For exmaple, if your
    popup contains links ("a" element), menu will interfere with the formatting.

Popup can be placed alongside the other element and event can be linked up::

    $m = $app->add('Menu');
    $item = $m->addItem('HoverMe');

    $app->add(['Popup', 'triggerBy'=>$item, 'triggerOn'=>'hover'])->set(function($popup) {
        $popup->add('Text')->set('Appears when you hover a menu item');
        $popup->add(['Label', 'Random value', 'detail'=>rand(1,100)]);
    });

Demo: http://ui.agiletoolkit.org/demos/popup.php

Semantic UI: https://semantic-ui.com/modules/popup.html

