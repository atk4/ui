


.. _js:

==================
JavaScript Mapping
==================

A modern user interface cannot exist without JavaScript. For Agile UI to work, certain views
must be able to bind with JavaScript frameworks.

.. important::
    
    We never encourage writing JavaScript logic in PHP. The purpose of JS layer is for binding
    events and actions.

One of the most popular JavaScript library - jQuery - uses chaining extensively. Syntax is easy
to understand and write:

.. code-block:: js

    $('.mybox').find('button').click(function() {
        $('.otherbox').hide();
    });

In most cases you will be using 3rd party plug-ins to simply initialize stuff or bind events.
If you are looking to build a DropDown view that needs to initialize itself on the JavaScript end
with:

.. code-block:: js

<<<<<<< HEAD
    $('#the-box-id).dropdown();

the initialization should somehow embedded inside your own DropDown view. Of course you can hide
piece of JavaScript inside your custom HTML template, but that's a anti-pattern with Agile UI that
tries to steamline all the UI.
=======
    $('#the-box-id').dropdown();

the initialization should somehow be embedded inside your own DropDown view. Of course you can hide
piece of JavaScript inside your custom HTML template, but that is a anti-pattern with Agile UI that
tries to streamline all the UI.
>>>>>>> develop


Agile UI relies on two important mechanics that makes JavaScript interaction simple.

JavaScript Chain Building
-------------------------

.. php:namespace: atk4\\ui
.. php:class:: jsChain

    Base class jsChain can be extended by other classes such as jQuery to provide transparent
    mappers for any JavaScript framework.

Chain is a PHP object that represents one or several actions that are to be executed on the 
client side::

    $chain = new jQuery('#the-box-id');

    $chain->dropdown();

The calls to the chain are stored in the object and can be converted into JavaScript by calling :php:meth:`jsChain::jsRender()`

.. php:method:: jsRender()

    Converts actions recorded in jsChain into string of JavaScript code.


Executing::

    echo $chain->jsRender();

will output:

.. code-block:: js

    $('#the-box-id').dropdown();

View to JS integration
----------------------

We are not building JavaScript code just for the excercise. Our whole point is ability to link that code
between actual views. All views support JavaScript binding through two methods: :php:meth:`View::js()` and :php:meth:`View::on()`.

.. php:class:: View
.. php:method:: js([$event])

    Return chain corresponding to the view.

.. php:method:: on(String $event, [String selector], $callback = null)

    Returns chain that will be automatically executed if $event occurs. If $callback is specified, it
    will also be executed on event.

Calling $button->js() you will get a new jQuery chain object that you can interact with::

    $chain = $form->js()->hide('slow');

Just on it's own chain will not do anything, so you can use "on" method to bind the action::

    $button->on('click', $chain);

Mechanics of Chains and Binding form a powerful concept, but to make it as powerful as possible, we
have implemented a lot of ways for you to be expressive in the PHP.

Finally I must mention that js() have an argument for event:

- omitted, false or null - chain will be returned only.
- true - executes chain onDocumentReady
- string (like "click") - specify specific event

So if you want button to hide itself when clicked, this simple syntax can be used::

    $button->js('click')->hide();

More commonly you will want to execute chains onDocumentReady::

    $dropdown_field->js(true)->dropdown();


jsExpressionable and jsExpression
=================================

.. php:interface:: jsExpressionable

    Some of the clases that implement jsExpressionable are:
     
    - jsExpression
    - jsChain
    - View


.. php:class:: jsExpression
.. php:method:: __construct(template, args)

    Returns object that renders into template by substituting args into it.

This interface can be implemented by the object and would mean that this object can be mapped into
a safe JavaScript code. Any other variables will be passed to `json_encode` when they are parts of
expression.

Compare next two examples::

    echo (new jQuery('document'))->find('h1')->hide()->jsRender();

    // produces $('document').find('h1').hide();
    // does not hide anything because document is streated as string selector!

    $expr = new jsExpression('document');
    echo (new jQuery($expr))->find('h1')->hide()->jsRender();

    // produces $(document).find('h1').hide();
    // works correctly!!

Template of jsExpression
------------------------

The jsExpression class provides the most simple implementation that can be useful for providing
any JavaScript expressions. My next example will set height of right container to the sum of 2
boxes on the left::

    $h1 = $left_box1->js()->height();
    $h2 = $left_box2->js()->height();

    $sum = new jsExpression('[]+[]', [$h1, $h2]);

    $right_box_container->js(true)->height( $sum );

It is important that you remember that height of an element is a browser-side property and you
must operate with it in your browser by passing expressions into chain.


The template language for jsExpression is super-simple:

 - [] will be mapped to next argument in the argument array
 - [foo] will be mapped to named argument in argument array

So the following three lines are identical::

    $sum = new jsExpression('[]+[]', [$h1, $h2]);
    $sum = new jsExpression('[0]+[1]', [0=>$h1, 1=>$h2]);
    $sum = new jsExpression('[a]+[b]', ['a'=>$h1, 'b'=>$h2]);

.. important:: 

    We have specifically selected a very simple tag format as a reminder to you not to write
    any code as part of jsExpression. You must not use jsExpression() for anything complex.


Writing JavaScript code
-----------------------

Open a new file `test.js` and type:

.. code-block:: js

    function mySum(arr) {
        return arr.reduce(function(a, b) { 
            return a+b;
        }, 0);
    }

When load this js dependency on your page, then you can use the following chain::

    $heights = [];

    foreach ($left_container->elements as $left_box) {
        $heights[] = $left_box->js()->height();
    }

    $right_container->js(true)->height(new jsExpression('mySum([])', [$heights]));

This will map into the following JavaScript code:

.. code-block:: js

    $('#right_container_id').height(mySum([
        $('#left_box1').height(), $('#left_box2').height(), $('#left_box3').height() // etc
    ]));

You can further simplify JavaScript code yourself, but keep the JavaScript logic inside the `.js` files
and leave PHP only for binding.
