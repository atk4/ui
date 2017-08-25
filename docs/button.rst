
.. _button:

======
Button
======

.. php:namespace:: atk4\ui

.. php:class:: Button

Implements a clickable button::

    $button = $view->add(new \atk4\ui\Button('Click me'));

The button will typically inherit all same properties of a :php:class:`View`. Functionality
of View alone yields in many various usage patterns such as::

    $b1 = new Button(['Load', 'primary']);

    $button = new Button('Hello there');
    $button->addClass('size big');


You can refer to the Semantic UI documentation for Button to find out more about available classes: http://semantic-ui.com/elements/button.html.

Demo: http://ui.agiletoolkit.org/demos/button.php

Button Icon
-----------

.. php:attr:: icon

Includes icon on the button::

    $bar = new Buttons('vertical');  // NOTE: class called Buttons, not Button
    $bar->add(new Button(['Play', 'icon'=>'play']));
    $bar->add(new Button(['Pause', 'icon'=>'pause']));
    $bar->add(new Button(['Shuffle', 'icon'=>'shuffle']));

Icon can also be specified as an object::

    $b1 = new Button(['Forks', 'blue', 'icon'=>new Icon('fork'));

.. php:attr:: iconRight

Setting this will display icon on the right of the button::


    $b1 = new Button(['Next', 'iconRight'=>'right arrow']);

Apart from being on the right, the same rules apply as :php:attr:`Button::$icon`. Both
icons cannot be specified simultaniously.

Linking
-------

.. php:method:: link

Will link button to a destination URL or page::

    $button->link('http://google.com/');
    // or
    $button->link(['details', 'id'=>123]);

If array is used, it's routed to :php:meth:`App::url`

For other JavaScript actions you can use :ref:`js`::

    $button->js('click', new jsExpression('document.location.reload()'));

Complex Buttons
---------------

Knowledge of the Semantic UI button (http://semantic-ui.com/elements/button.html) can help you
in creating more complex buttons::

    $forks = new Button(['labeled'=> true]); // Button, not Buttons!
    $forks->add(new Button(['Forks', 'blue']))->add(new Icon('fork'));
    $forks->add(new Label(['1,048', 'basic blue left pointing']));
    $layout->add($forks);
