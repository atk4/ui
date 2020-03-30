
Callback Introduction
---------------------

Agile UI pursues a goal of creating a full-featured, interractive, user interface. Part of that relies
on abstraction of Browser/Server communication.

Callback mechanism allow any :ref:`component` of Agile Toolkit to send HTTP requests back to itself
through a unique route and not worry about accidentally affecting or triggering action of any other
component.

One example of this behaviour is the format of :php:meth:`View::on` where you pass 2nd argument as a
PHP callback::

    $button = new Button();

    // clicking button generates random number every time
    $button->on('click', function($action){
        return $action->text(rand(1,100));
    });

This creates call-back route transparently which is triggered automatically during the 'click' event.
To make this work seamlessly there are several classes at play. This documentation chapter will walk
you through the callback mechanisms of Agile UI.

The Callback class
------------------

.. php:class:: Callback

Callback is not a View. This class does not extend any other class but it does implement several important
traits:

 - `TrackableTrait <https://agile-core.readthedocs.io/en/develop/container.html?highlight=trackable#trackable-trait>`_
 - `AppScopeTrait <https://agile-core.readthedocs.io/en/develop/appscope.html>`_
 - `DIContainerTrait <https://agile-core.readthedocs.io/en/develop/di.html>`_

To create a new callback, do this::

    $c = new \atk4\ui\Callback();
    $app->add($c);

Because 'Callback' is not a View, it won't be rendered. The reason we are adding into :ref:`render_tree`
is for it to establish a unique name which will be used to generate callback URL:

.. php:method:: getURL($val)

.. php:method:: set

The following example code generates unique URL::

    $label = \atk4\ui\Label::addTo($app, ['Callback URL:']);
    $cb = \atk4\ui\Callback::addTo($label);
    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

I have assigned generated URL to the label, so that if you click it, your browser will visit
callback URL triggering a special action. We haven't set that action yet, so I'll do it next with
:php:meth::`Callback::set()`::

    $cb->set(function() use($app) {
        $app->terminate('in callback');
    });

Callback Triggering
-------------------
To illustrate how callbacks work, let's imagine the following workflow:

 - your application with the above code resides in file 'test.php`
 - when user opens 'test.php' in the browser, first 4 lines of code execute
   but the set() will not execute "terminate". Execution will continue as normal.
 - getURL() will provide link e.g. `test.php?app_callback=callback`

When page renders, the user can click on a label. If they do, the browser will send
another request to the server:

 - this time same request is sent but with the `?app_callback=callback` parameter
 - the :php:meth:`Callback::set()` will notice this argument and execute "terminate()"
 - terminate() will exit app execution and output 'in callback' back to user.

Calling :php:meth:`App::terminate()` will prevent the default behaviour (of rendering UI) and will
output specified string instead, stopping further execution of your application.

Return value of set()
---------------------

The callback verifies trigger condition when you call :php:meth:`Callback::set()`. If your callback
returns any value, the set() will return it too::

    $label = \atk4\ui\Label::addTo($app, ['Callback URL:']);
    $cb = \atk4\ui\Callback::addTo($label);
    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

    if($cb->set(function(){ return true; })) {
        $label->addClass('red');
    }

This example uses return of the :php:meth:`Callback::set()` to add class to a label, however a
much more preferred way is to use :php:attr:`$triggered`.

.. php:attr:: triggered

You use property `triggered` to detect if callback was executed or not, without short-circuting the
execution with set() and terminate(). This can be helpful sometimes when you need to affect the
rendering of the page through a special call-back link. The next example will change color of
the label regardless of the callback function::

    $label = \atk4\ui\Label::addTo($app, ['Callback URL:']);
    $cb = \atk4\ui\Callback::addTo($label);
    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

    $cb->set(function(){ echo 123; });

    if ($cb->triggered) {
        $label->addClass('red');
    }

.. php:attr:: postTrigger

A Callback class can also use a POST variable for triggering. For this case the $callback->name should be set
through the POST data.

Even though the functionality of Callback is very basic, it gives a very solid foundation for number of
derived classes.

.. php:attr:: urlTrigger

Specifies which GET parameter to use for triggering. Normally it's same as `$callback->name`, but you can set it
to anything you want. As long as you keep it unique on a current page, you should be OK.

CallbackLater
-------------

.. php:class:: CallbackLater

This class is very similar to Callback, but it will not execute immediatelly. Instead it will be executed
either at the end at beforeRender or beforeOutput hook from inside App, whichever comes first.

In other words this won't break the flow of your code logic, it simply won't render it. In the next example
the $label->detail is assigned at the very end, yet callback is able to access the property::

    $label = \atk4\ui\Label::addTo($app, ['Callback URL:']);
    $cb = \atk4\ui\CallbackLater::addTo($label);

    $cb->set(function() use($app, $label) {
        $app->terminate('Label detail is '.$label->detail);
    });

    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

CallbackLater is used by several actions in Agile UI, such as jsReload(), and ensures that the component
you are reloading are fully rendered by the time callback is executed.

Given our knowledge of Callbacks, lets take a closer look at how jsReload actually works. So what do we
know about :php:class:`jsReload` already?

 - jsReload is class implementing jsExpressionable
 - you must specify a view to jsReload
 - when triggered, the view will refresh itself on the screen.

Here is example of jsReload::

    $view = \atk4\ui\View::addTo($app, ['ui'=>'tertiary green inverted segment']);
    $button = \atk4\ui\Button::addTo($app, ['Reload Lorem']);

    $button->on('click', new \atk4\ui\jsReload($view));

    \atk4\ui\LoremIpsum::addTo($view);


NOTE: that we can't perform jsReload on LoremIpsum directly, because it's a text, it needs to be inside
a container. When jsReload is created, it transparently creates a 'CallbackLater' object inside
`$view`. On the JavaScript side, it will execute this new route which will respond with a NEW content
for the $view object.

Should jsReload use regular 'Callback', then it wouldn't know that $view must contain LoremIpsum text.

jsReload existance is only possible thanks to CallbackLater implementation.


jsCallback
----------

.. php:class:: jsCallback

So far, the return value of callback handler was pretty much insignificant. But wouldn't it be great if this
value was meaningful in some way?

jsCallback implements exactly that. When you specify a handler for jsCallback, it can return one or multiple :ref:`js_action`
which will be rendered into JavaScript in response to triggering callback's URL. Let's bring up our older example, but will
use jsCallback class now::

    $label = \atk4\ui\Label::addTo($app, ['Callback URL:']);
    $cb = \atk4\ui\jsCallback::addTo($label);

    $cb->set(function() {
        return 'ok';
    });

    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

When you trigger callback, you'll see the output::

    {"success":true,"message":"Success","eval":"alert(\"ok\")"}

This is how jsCallback renders actions and sends them back to the browser. In order to retrieve and execute actions,
you'll need a JavaScript routine. Luckily jsCallback also implements jsExpressionable, so it, in itself is an action.

Let me try this again. jsCallback is an :ref:`js_action` which will execute request towards a callback-URL that will
execute PHP method returning one or more :ref:`js_action` which will be received and executed by the original action.

To fully use jsAction above, here is a modified code::

    $label = \atk4\ui\Label::addTo($app, ['Callback URL:']);
    $cb = \atk4\ui\jsCallback::addTo($label);

    $cb->set(function() {
        return 'ok';
    });

    $label->detail = $cb->getURL();
    $label->on('click', $cb);

Now, that is pretty long. For your convenience, there is a shorter mechanism::

    $label = \atk4\ui\Label::addTo($app, ['Callback test']);

    $label->on('click', function() {
        return 'ok';
    });

User Confirmation
^^^^^^^^^^^^^^^^^

The implementation perfectly hides existence of callback route, javascript action and jsCallback. The jsCallback
is based on 'Callback' therefore code after :php:meth:`View::on()` will not be executed during triggering.

.. php:attr:: confirm

If you set `confirm` property action will ask for user's confirmation before sending a callback::

    $label = \atk4\ui\Label::addTo($app, ['Callback URL:']);
    $cb = \atk4\ui\jsCallback::addTo($label);

    $cb->confirm = 'sure?';

    $cb->set(function() {
        return 'ok';
    });

    $label->detail = $cb->getURL();
    $label->on('click', $cb);

This is used with delete operations. When using :php:meth:`View::on()` you can pass extra argument to set the 'confirm'
property::

    $label = \atk4\ui\Label::addTo($app, ['Callback test']);

    $label->on('click', function() {
        return 'ok';
    }, ['confirm'=>'sure?']);

JavaScript arguments
^^^^^^^^^^^^^^^^^^^^

.. php:method:: set($callback, $arguments = [])

It is possible to modify expression of jsCallback to pass additional arguments to it's callback. The next example
will send browser screen width back to the callback::

    $label = \atk4\ui\Label::addTo($app);
    $cb = \atk4\ui\jsCallback::addTo($label);

    $cb->set(function($j, $arg1){
        return 'width is '.$arg1;
    }, [new \atk4\ui\jsExpression( '$(window).width()' )]);

    $label->detail = $cb->getURL();
    $label->js('click', $cb);

In here you see that I'm using a 2nd argument to $cb->set() to specify arguments, which, I'd like to fetch from the
browser. Those arguments are passed to the callback and eventually arrive as $arg1 inside my callback. The :php:meth:`View::on()`
also supports argument passing::

    $label = \atk4\ui\Label::addTo($app, ['Callback test']);

    $label->on('click', function($j, $arg1) {
        return 'width is '.$arg1;
    }, ['confirm'=>'sure?', 'args'=>[new \atk4\ui\jsExpression( '$(window).width()' )]]);

If you do not need to specify confirm, you can actually pass arguments in a key-less array too::

    $label = \atk4\ui\Label::addTo($app, ['Callback test']);

    $label->on('click', function($j, $arg1) {
        return 'width is '.$arg1;
    }, [new \atk4\ui\jsExpression( '$(window).width()' )]);


Refering to event origin
^^^^^^^^^^^^^^^^^^^^^^^^

You might have noticed that jsCallback now passes first argument ($j) which so far, we have ignored. This argument is a
jQuery chain for the element which received the event. We can change the response to do something with this element.
Instead of `return` use::

    $j->text('width is '.$arg1);

Now instead of showing an alert box, label content will be changed to display window width.

There are many other applications for jsCallback, for example, it's used in :php:meth:`Form::onSubmit()`.


