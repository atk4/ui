

Introduction
------------

Ability to automatically generate callback URLs is one of the unique features in Agile UI.
With most UI widgets they would rely on a specific URL to be available or would require
you to define them.

With Agile UI the backend URLs are created dynamically by using unique names and callbacks.

There is one problem, however. What if View (and the callbacks too) are created conditionally?

The next code creates Loader area which will display a console. Result is - nested callback::

    Loader::addTo($app)->set(function (Loader $p) {
        Console::addTo($p)->set(function (Console $console) {
            $console->output('success!');
        });
    });

What if you need to pass a variable `client_id` to display on console output? Technically you
would need to tweak the callback URL of "Loader" and also callback URL of "Console".

Sticky GET is a better approach. It works like this::

    $app->stickyGet('client_id');

    Loader::addTo($app)->set(function (Loader $p) {
        Console::addTo($p)->set(function (Console $console) {
            $console->output('client_id = !' . $_GET['client_id']);
        });
    });

Whenever Loader, Console or any other component generatens a URL, it will now include value
of `$_GET['client_id']` and it will transparently arrive inside your code even if it takes
multiple requests to get there.


Global vs Local Sticky GET
^^^^^^^^^^^^^^^^^^^^^^^^^^

In earlier example, we have called `$app->stickyGet` which creates a global stickyGet. After
executing, all the invocations to App::url() or View::url() will contain "client_id".

In some cases, Sticky GET only make sense within a certain branch of a Render Tree. For instance,
when Loader wishes to load content dynamically, it must pass extra _GET parameter to trigger a
:php:class:`Callback`. Next, when Console needs to establish live SSE stream, it should include
the SAME get argument to trigger a callback for the Loader, otherwise Console wouldn't be
initialized at all.

Loader sets a local stickyGet on the $p before it's passed inside your function::

    $p->stickyGet('trigger_name');

This way - all the views added into this $p will carry an extra get argument::

    $p->url(); // includes "trigger_name=callback"

If you call `$app->url()` it will contain `client_id` but won't contain the callbacks triggers.

View Reachability
^^^^^^^^^^^^^^^^^

Agile UI views have a method View::url() which will return URL that is guaranteed to trigger their "init"
method. This is regardless of the placement of your View and also it honors all the arguments that are
defined as sticky globally.

Consider this code::

    $b1 = \Atk4\Ui\Button::addTo($app);
    $b1->set($b1->url());

    Loader::addTo($app)->set(function (Loader $p) {
        $b2 = \Atk4\Ui\Button::addTo($p);
        $b2->set($b2->url());
    });

    $b3 = \Atk4\Ui\Button::addTo($app);
    $b3->set($b3->url());

This will display 3 buttons and each button will contain a URL which needs to be opened in order for
corresponding button to be initialized. Because middle button is inside a callback the URL for that
will be different.


Dropping sticky argument
^^^^^^^^^^^^^^^^^^^^^^^^

Sometimes you want to drop a sticky argument. If your sticky was set locally, you can drop it by calling
either a parent's url or $app->url(), however for global sticky Get you can use either `url(['client_id' => false])`
or `stickyForget('client_id')`.


