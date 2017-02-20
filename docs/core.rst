=============
Core Concepts
=============

Before diving into individual features of Agile UI, there are several core concepts that
will be referred to throughout the documentation.

.. _app:

Application and Layout
======================

When writing application that uses Agile UI you can either select to use individual components
or make them part of a bigger layout. If you use the component individually, then it will
at some point initialize internal 'App' class that will assist with various tasks.

Having composition of multiple components will allow them to share the app object::

    $grid = new \atk4\ui\Grid();
    $grid->setModel($user);
    $grid->addPaginator();          // initialize and populare paginator
    $grid->addButton('Test');       // initialize and populate toolbar

    echo $grid->render();

All of the objects created above - button, grid, toolbar and paginator will share same
value for the 'app' property. This value is carried into new objects through AppScopeTrait
(http://agile-core.readthedocs.io/en/develop/appscope.html).

Adding the App
--------------

You can create App object on your own then add elements into it::

    $app = new App('My App');
    $app->add($grid);

    echo $grid->render();

This does not change the output, but you can use the 'App' class to your advancage as a
"Property Bag" pattern to inject your configuration. You can even use a different "App"
class alltogether, which is how you can affect the default generation of links, reading
of GET/POST data and more.

We are still not using the layout, however.

Adding the Layout
-----------------

Layout can be initialized through the app like this::

    $app->initLayout('Centered');

This will initialize two new views inside the app::

    $app->html 
    $app->layout

The first view is a HTML boilerplate - containing HEAD / BODY tags but not the body
contents. It is a standard html5 doctype template.

The layout will be selected based on your choice - 'Centered', 'Admin' etc. This will
not only change the overal page outline, but will also introduce some additional views.

Going with the 'Admin' layout will populate some menu objects. Each layout may come with
several views that you can populate::

    $app->initLayout('Admin');

    // Add item into menu
    $app->layout->menu->addItem('User Admin', 'admin');

Integration with Legacy Apps
----------------------------

If you use Agile UI inside a legacy application, then you may already have layout and some
patterns or limitations may be imposed on the app. Your first job would be to properly
implement the "App" and either modification of your exsiting class or a new class.

Having a healthy "App" class will ensure that all of Agile UI components will perform
properly.

3rd party Layouts
-----------------

You should be able to find 3rd party Layout implementations that may even be coming with
some custom templates and views. The concept of a "Theme" in Agile UI consists of
offering the following 3 things:

 - custom CSS build from Semantic UI
 - custom Layout(s) along with documentation
 - additional or tweaked Views

Unique layouts can be used to change the default look and as a stand-in replacement to
some of standard layouts or as a new and entirely different layout.


.. _seed:

Seed
====

When creating a view, you have a chance to pass an argument to it. We have decided to
refer to this special argument as "seed" because it has multiple purposes and the structure
may differ depending on the element.

The most trivial case of seeding is::

    $button = new Button('Hello');

Here button is seeded with a string and the button interprets it by setting a label. Other
Views may interpret seed differenttly, Icon will convert seed into a class::

    $icon = new Icon('book');

The seed designed to be intuitive for reading and remembering rather than attaching it
to a specific technical property. Here are some more examples::

    $app = new App('Hello World'); // name of the app

Empty Seed
----------

Some views may not use a seed (yet), they will still accept an empty seed::

    $app = new App();       // will use name = 'Untitled'
    $form = new Form();     // no name yet


Dependency Injection
--------------------

Seed is a great way for you to perform dependency injection because seed argument may
be an array. If seed is specified as "array", then the value with index "0" will have
identical effect as not using array::

    $button = new Button('Hello');

    // same as

    $button = new Button(['Hello']);

Once the zero-indexed value is located and extracted from the seed, the rest of the array
will be used as a dependency-injection or "defaults"::

    $button = new Button(['Learn', 'icon'=>new Icon('book')]);

This will set the "icon" property of a Button class to the specified value (object). Setting
of an object properties in only possible, if the property is declared. Attempt to set
non-existant property will result in exception::

    $button = new Button(['Learn', 'my_property'=>123]);

Additional cases
----------------

An individual object may add more ways to deal with seed. For example, when dealing with button
you can specify both the label and the class through the seed::

    $button = new Button(['Learn', 'big teal', 'icon'=>new Icon('book')]);

The view will generally map non-existing property seeds into HTML class, although it is recommended
to use :php:meth:`View::addClass` method::

    $button = new Icon(['book', 'red'=>true]);

    // same as

    $button = new Icon('book');
    $button->addClass('red');

    // or because it's a button
    $button = new Icon('red book');


.. _no_data:

Use without Agile Data
======================

Agile UI relies on Agile Data library for flexible access to user defined data sources. The purpose of this integration
is to relieve developer from manually creating data fetching and storing code.

Other benefits of relying on Agile Data models is ability to store meta information of the models themselves. Without
Agile UI as hard dependency, Agile UI would have to re-implement all those features on it's own resulting in much
bigger code footprint.

There are no way to use Agile UI without Agile Data, however Agile Data is flexibly enough to work with your own
data sources. The rest of this chapter will explain how you can map various data structures.

Static Data Arrays
------------------

Agile Data contains Persistence_Array (http://agile-data.readthedocs.io/en/develop/design.html?highlight=array#domain-model-actions)
implementation that load and store data in a regular PHP arrays. For the "quick and easy" solution Agile UI Views provide a
method :php:meth:`View::setSource` which will work-around complexities and give you a syntax::

    $grid->setSource([
        1 => ['name'=>'John', 'surname'=>'Smith', 'age'=>10],
        2 => ['name'=>'Sarah', 'surname'=>'Kelly', 'age'=>20],
    ]);

.. note:: 
    Dynamic views will not be able to identify that you are working with static data and some features may not work properly.
    There are no plans in Agile UI to improve ways of using "setSource", instead you should learn more how to use Agile Data
    for expressing your native data source. Agile UI is not optimized for setSource so it's performance will generally be
    slower too.

Raw SQL Queries
---------------

Writing raw SQL queries is source of many errors, both with a business logic and security. Agile Data provides great ways
for abstracting your SQL queries, but if you have to use a raw query::

    // not sure how TODO - write this section.

.. note::
    The above way to using raw queries has a performance implications, because Agile UI is optimised to work with Agile
    Data.

Factories
=========


Render Tree
===========
