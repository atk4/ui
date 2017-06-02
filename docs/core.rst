=============
Core Concepts
=============

Agile Toolkit and Agile UI is built by following the core concepts. Understanding the
concepts is very important especially if you plan to write and distribute your own
add-ons.

App
===

In any Agile UI application you would always need to have an App class. Even if you do not
create this class explicitly, components generally will do it for you, but the common pattern
is::

    $app = new \atk4\ui\App('My App');
    $app->initLayout('Centered');
    $app->layout->add('LoremIpsum');

.. toctree::
    app


Seed
====
Agile UI is developed to be easy to read and with simple and consise syntax. We make use of
dynamic nature of PHP, therefore two syntax patterns are supported everywhere::

    $app->layout->add(new \atk4\ui\Button('Hello'));

    and

    $app->layout->add(['Button', 'Hello']);

Method add() supports arguments in a various formats and we call that "Seed". The same format
can be used elsewhere, for example::

    $button->icon = 'book';

We call this format 'Seed' this section will explain how and where it is used.

.. toctree::
    seed



Render Tree
===========
Agile Toolkit is allows you to create components hierarchically. What is really unique and
awesome is that you can create and link multiple UI objects together before linking them
with other chunks of your UI::

    $msg = new \atk4\ui\Message('Hey There');
    $msg->add(new \atk4\ui\Button('Button'));

    $app->layout->add($msg);

To find out more about how components are linked up together and rendered, see:

.. toctree::
    render

Templates
=========
Agile UI components store their HTML inside `*.html` template files. Those files are loaded
and manipulated by a Template class.

To learn more on how to create a custom template or how to change global template
behaviour see:

.. toctree::
    template



Agile Data Integration
======================
Agile UI framework is focused on building User Interfaces, but quite often interface must
present data values to the user or even receive data values from user's input.

Agile UI uses various techniques to present data formats, so that as a developer you wouldn't
have to sweat the details::

    $user = new User($db);
    $user->load(1);

    $view = $app->layout-add(['template'=>'Hello, {$name}, your balance is {$balance}']);
    $view->setModel($user);

Next section will explain you how Agile UI interacts with the data layer and how it outputs or
inputs user data.

.. toctree::
    data



.. toctree::
    :maxdepth: 4

    init
    template
    callback
    virtualpage

