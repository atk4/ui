=============
Core Concepts
=============

.. php:namespace:: atk4\ui

Agile Toolkit and Agile UI are built upon specific core concepts. Understanding those
concepts is very important especially if you plan to write and distribute your own
add-ons.

App
===

In any Agile UI application you will always need to have an App class. Even if you do not
create this class explicitly, components generally will do it for you. The common pattern
is::

    $app = new \atk4\ui\App('My App');
    $app->initLayout(\atk4\ui\Layout\Centered::class);
    LoremIpsum::addTo($app);

.. toctree::
    app

.. _seed:

Seed
====
Agile UI is developed to be easy to read and with simple and concise syntax. We make use of
PHP's dynamic nature, therefore two syntax patterns are supported everywhere::

    Button::addTo($app, ['Hello']);

    and

    Button::addTo($app, ['Hello']);

Method add() supports arguments in various formats and we call that "Seed". The same format
can be used elsewhere, for example::

    $button->icon = 'book';

We call this format 'Seed'. This section will explain how and where it is used.

.. toctree::
    seed

.. _render:
.. _render_tree:

Render Tree
===========
Agile Toolkit allows you to create components hierarchically. Once complete, the component
hierarchy will render itself and will present HTML output that would appear to user.

You can create and link multiple UI objects together before linking them with other chunks of your UI::

    $msg = new \atk4\ui\Message('Hey There');
    Button::addTo($msg, ['Button']);

    $app->add($msg);

To find out more about how components are linked up together and rendered, see:

.. toctree::
    render

Sticky GET
==========
Agile UI implements advanced approach allowing any View object that you add into Render Tree to
declare "sticky GET arguments". Here is example::

    if(isset($_GET['message'])) {
        Message::addTo($app)->set($_GET['message']);
    }

    Button::addTo($app, ['Trigger message'])->link(['message'=>'Hello World']);

The code is simple - if you click the button, page will appear with the message just above, however
there is a potential problem here. What if "Message" wanted to perform a :ref:`Callback`? What if
we use :php:class:`Console` instead, which must display an interactive data stream?

In Agile UI you can request that some $_GET arguments are preserved and included into callback urls::

    if($this->app->stickyGet('message')) {
        Message::addTo($app)->set($_GET['message']);
    }

    Button::addTo($app, ['Trigger message'])->link(['message'=>'Hello World']);

There are two types of "sticky" parameters, application-wide and view-specific.

.. toctree::

    sticky

Type Presentation
=================

Several components are too complex to be implemented in a single class. :php:class:`Table`, for example,
has the ability to format columns by utilizing type-specific column classes. Another example is :php:class:`Form`
which relies on Field-specific FormField component.

Agile UI uses a specific pattern for those definitions, which makes the overall structure more extensible
by having the ability to introduce new types with consistent support throughout the UI.

.. toctree::
    type-presentation



Templates
=========
Agile UI components store their HTML inside `*.html` template files. Those files are loaded
and manipulated by a Template class.

To learn more on how to create a custom template or how to change global template
behavior see:

.. toctree::
    template



Agile Data
==========

Agile UI framework is focused on building User Interfaces, but quite often interface must
present data values to the user or even receive data values from user's input.

Agile UI uses various techniques to present data formats, so that as a developer you wouldn't
have to worry over the details::

    $user = new User($db);
    $user->load(1);

    $view = View::addTo($app, ['template'=>'Hello, {$name}, your balance is {$balance}']);
    $view->setModel($user);

Next section will explain you how the Agile UI interacts with the data layer and how it outputs or
inputs user data.

.. toctree::
    data

.. _callback:

Callbacks
=========

By relying on the ability of generating :ref:`unique_name`, it's possible to create several classes
for implementing PHP call-backs. They follow the pattern:

 - present something on the page (maybe)
 - generate URL with unique parameter
 - if unique parameter is passed back, behave differently

Once the concept is established, it can even be used on a higher level, for example::

    $button->on('click', function() { return 'clicked button'; });

.. toctree::
    :maxdepth: 4

    callbacks


.. _virtualpage:

VirtualPage
===========

Building on the foundation of :ref:`callback`, components :php:class:`VirtualPage` and :php:class:`Loader`
exist to enhance other Components with dynamically loadable content. Here is example for :php:class:`Tabs`::

    $tabs = Tabs::addTo($app);
    LoremIpsum::addTo($tabs->addTab('First tab is static'));

    $tabs->addTab('Second tab is dynamic', function($vp) {
        LoremIpsum::addTo($vp);
    });

As you switch between those two tabs, you'll notice that the :php:class:`Button` label on the "Second tab"
reloads every time. :php:class:`Tabs` implements this by using :php:class:`VirtualPage`, read further to
find out how:


.. toctree::
    :maxdepth: 4

    virtualpage



Documentation is coming soon.
=============================

.. toctree::
    :maxdepth: 4

    init
    callback
    stickyget

