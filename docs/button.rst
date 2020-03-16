
.. _button:

======
Button
======

.. php:namespace:: atk4\ui

.. php:class:: Button

Implements a clickable button::

    $button = $app->add(['Button', 'Click me']);

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

    $button = $app->add(['Button', 'Like', 'blue', 'icon'=>'thumbs up']);

    // or

    $button = $app->add(['Button', 'Like', 'blue', 'icon'=>new Icon('thumbs up')]);

or if you prefer initializing objects::

    $button = new Button('Like');
    $button->addClass('blue');
    $button->icon = new Icon('thumbs u');

    $app->add($button);

.. php:attr:: iconRight

Setting this will display icon on the right of the button::


    $button = $app->add(['Button', 'Next', 'iconRight'=>'right arrow']);

Apart from being on the right, the same rules apply as :php:attr:`Button::$icon`. Both
icons cannot be specified simultaniously.

Button Bar
----------

Buttons can be aranged into a bar. You would need to create a :php:class:`View` component
with property ``ui='buttons'`` and add your other buttons inside::

    $bar = $app->add(['View', 'ui'=>'vertical buttons']);

    $bar->add(['Button', 'Play', 'icon'=>'play']);
    $bar->add(['Button', 'Pause', 'icon'=>'pause']);
    $bar->add(['Button', 'Shuffle', 'icon'=>'shuffle']);

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
    $forks->add(new Button(['Forks', 'blue']))->add(new Icon('fork'));
    $forks->add(new Label(['1,048', 'basic blue left pointing']));
    $app->add($forks);

