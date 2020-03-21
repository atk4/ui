
.. _button:

======
Button
======

.. php:namespace:: atk4\ui

.. php:class:: Button

Implements a clickable button::

    $button = Button::addTo($app, ['Click me']);

The Button will typically inherit all same properties of a :php:class:`View`. The base class "View"
implements many useful methods already, such as::

    $button->addClass('big red');

Alternatvie syntax if you wish to initialize object yourself::

    $button = new Button('Click me');
    $button->addClass('big red');

    $app->add($button);


You can refer to the Fomantic UI documentation for Button to find out more about available classes: https://fomantic-ui.com/elements/button.html.

Demo: https://ui.agiletoolkit.org/demos/button.php

Button Icon
-----------

.. php:attr:: icon

Property $icon will place icon on your button and can be specified in one of the following two ways::

    $button = Button::addTo($app, ['Like', 'blue', 'icon'=>'thumbs up']);

    // or

    $button = Button::addTo($app, ['Like', 'blue', 'icon'=>new Icon('thumbs up')]);

or if you prefer initializing objects::

    $button = new Button('Like');
    $button->addClass('blue');
    $button->icon = new Icon('thumbs u');

    $app->add($button);

.. php:attr:: iconRight

Setting this will display icon on the right of the button::


    $button = Button::addTo($app, ['Next', 'iconRight'=>'right arrow']);

Apart from being on the right, the same rules apply as :php:attr:`Button::$icon`. Both
icons cannot be specified simultaniously.

Button Bar
----------

Buttons can be aranged into a bar. You would need to create a :php:class:`View` component
with property ``ui='buttons'`` and add your other buttons inside::

    $bar = View::addTo($app, ['ui'=>'vertical buttons']);

    Button::addTo($bar, ['Play', 'icon'=>'play']);
    Button::addTo($bar, ['Pause', 'icon'=>'pause']);
    Button::addTo($bar, ['Shuffle', 'icon'=>'shuffle']);

At this point using alternative syntax where you initialize objects yourself becomes a bit too complex and lengthy::

    $bar = new View();
    $bar->ui = 'buttons';
    $bar->addClass('vertical');

    $button = new Button('Play');
    $button->icon = 'play';
    $bar->add($button);

    $button = new Button('Pause');
    $button->icon = 'pause';
    $bar->add($button);

    $button = new Button('Shuffle');
    $button->icon = 'shuffle';
    $bar->add($button);

    $app->add($bar);


Linking
-------

.. php:method:: link

Will link button to a destination URL or page::

    $button->link('https://google.com/');
    // or
    $button->link(['details', 'id'=>123]);

If array is used, it's routed to :php:meth:`App::url`

For other JavaScript actions you can use :ref:`js`::

    $button->js('click', new jsExpression('document.location.reload()'));

Complex Buttons
---------------



Knowledge of the Fomantic UI button (https://fomantic-ui.com/elements/button.html) can help you
in creating more complex buttons::

    $forks = new Button(['labeled'=> true]); // Button, not Buttons!
    Icon::addTo(Button::addTo($forks, ['Forks', 'blue']), ['fork']);
    Label::addTo($forks, ['1,048', 'basic blue left pointing']);
    $app->add($forks);

