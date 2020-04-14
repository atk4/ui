
.. _rightpanel:

===========
Right Panel
===========

.. php:namespace:: atk4\ui\Panel

.. php:class:: Right

Right panel are view attached to the app layout. They are display on demand via javascript event
and can display content statically or dynamically using Loadable Content.

Demo: https://ui.agiletoolkit.org/demos/layout-panel.php

Basic Usage
===========

Adding a right panel to the app layout and adding content to it::

    $panel = $app->layout->addRightPanel(new \atk4\ui\Panel\Right(['dynamic' => false]));
    Message::addTo($panel, ['This panel contains only static content.']);

By default, panel content are loaded dynamically. If you want to only add static content, you need to specify
the :ref:`dynamic` property and set it to false.

Opening of the panel is done via a javascript event. Here, we simply register a click event on a button that will open
the panel::

    $btn = Button::addTo($app, ['Open Static']);
    $btn->on('click', $panel->jsOpen());

Loading content dynamically
---------------------------

Loading dynamic content within panel is done via the onOpen method

.. php:method:: onOpen($callback)

Initializing a panel with onOpen callback::

    $panel_1 = $app->layout->addRightPanel(new \atk4\ui\Panel\Right());
    Message::addTo($panel_1, ['This panel will load content dynamically below according to button select on the right.']);
    $btn = Button::addTo($app, ['Button 1']);
    $btn->js(true)->data('btn', '1');
    $btn->on('click', $panel_1->jsOpen(['btn'], 'orange'));

    $panel_1->onOpen(function($p) {
        $btn_number = $_GET['btn'] ?? null;
        $text =  'You loaded panel content using button #' . $btn_number;
        Message::addTo($p, ['Panel 1', 'text' => $text]);
    });

.. php:method:: jsOpen

This method may take up to three arguments.

    $args: an array of data property to carry with the callback url. Let's say that you triggering element
    as a data property name id (data-id) then if specify, the data id value will be sent as a get argument
    with the callback url.

    $activeCss: a string representing the active state of the triggering element. This css class will be appied
    to the trigger element as long as the panel remains open. This help visualize, which element has trigger the
    panel opening.

    $jsTrigger: a jsExpression that represent the jQuery object where the data property reside. Default to $(this).
