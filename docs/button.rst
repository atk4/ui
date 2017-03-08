
.. _button:

======
Button
======

.. php:namespace:: atk4\ui\Button

.. php:class:: Button

Implements a clickable button::

    $button = $view->add(new \atk4\ui\Button('Click me'));

Button will typically inherit all same properties of a :php:class:`View`. Functionality
of View alone yields in many various usage patterns such as::

    $b1 = new Button(['Load', 'primary']);

    $button = new Button('Hello there');
    $button->addClass(['size big'=>true]);

Icons
-----

.. php:attr:: icon

Includes icon on the button::

    $bar = new Buttons('vertical');  // NOTE: class called Buttons, not Button
    $bar->add(new Button(['Play', 'icon'=>'play']));
    $bar->add(new Button(['Pause', 'icon'=>'pause']));
    $bar->add(new Button(['Shuffle', 'icon'=>'shuffle']));

Icon can also be specified as object::

    $b1 = new Button(['Forks', 'blue', 'icon'=>new Icon('fork'));

.. php:attr:: rightIcon

Setting this will display icon on the right of the button::


    $b1 = new Button(['Next', 'rightIcon'=>'right arrow']);

Apart from being on the right, same rules apply as :php:attr:`Button::$icon`. Both
icons can be specified simultaniously.

Linking
-------

.. php:method:: link

Will link button to a destination URL or page::

    $button->link('http://google.com/');
    // or
    $button->link(['details', 'id'=>123]);

If array is used, it's routed to :php:meth:`App::url`

For other JavaScript actions described :ref:`js` you can use::

    $button->js('click', new jsExpression('document.location.reload()'));



