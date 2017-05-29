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

Agile UI allows you to create and combine various objects into a single Render Tree for unified rendering. Tree represents
all the UI components that will contribute to the HTML generation. Render tree is automatically created and maintained::

    $view = new \atk4\ui\View();

    $view->add(new Button('test'));

    echo $view->render();

When render on the $view is executed, it will render button first then incorporate HTML into it's own template before rendering.

Here is a breakdown of how the above code works:

1. new instance of View is created and asigned to $view.
2. new instance of Button.
3. Button object is registered as a "pending child" of a view.

At this point Button is NOT element of a view just yet. This is because we can't be sure if $view will be rendered individually
or will become child of another view. Method init() is not executed on either objects.


4. render() method will call renderAll()
5. renderAll will find out that the $app property of a view is not set and will initialize it with default App.
6. renderAll will also find out that the init() has not been called for the $view and will call it.
7. init() will identify that there are some "pending children" and will add them in properly.

Most of the UI classes will allow you to operate even if they are not initialied. For instance calling 'setModel()' will
simply set a $model property and does not really need to rely on $api etc.

Next, lets look at what Initialization really is and why is it important.

Initialization
--------------

Calling init() method of a view is essential before any meaningfull work can be done with it. This is important, because
the following actions are performed:

 - template is loaded (or cloned from parent's template)
 - $app property is set
 - $short_name property is determined
 - unique $name is asigned.

Many of UI components rely on the above to function properly. For example Grid will look for certain regions in it's template
to clone them into separate objects. This cloning can only take place inside init() method.

Late initialization
-------------------

When you create an application and select a Layout, the layout is automatically initialized::

    $app = new \atk4\ui\App();
    $app->setLayout('Centered');

    echo $app->layout->name; // present, because layout is initalized!

After that, adding any objects into layout will initialize those objects too::

    $b = $app->layout->add(new Button('Test1'));o
    
    echo $b->name; // present, because button was added into initialized object.

If object cannot determine the path to the application, then it will remain uninitialized for some time. This is called
"Late initialization"::

    $v = new Buttons();
    $b2 = $v->add(new Button('Test2'));

    echo $b2->name; // not set!! Not part of render tree

At this point, if you execute $v->render() it will create it's own App and will create it's own render tree. On other hand
if you add $v inside layout, trees will merge and the same $app will be used::

    $app->layout->add($v);

    echo $b2->name; // fully set now and unique.

Agile UI will attempt to always initialize objects as soon as possible, so that you can get the most meaningful stack traces
should there be any problems with the initialization.


Rendering outside
-----------------

It's possible for some views to be rendered outside of the app. In the previous section I speculated that calling $v->render()
will create it's own tree independent from the main one. 

Agile UI sometimes uses the following approach to render element on the outside:

1. Create new instance of $sub_view.
2. Set $sub_view->id = false;
3. Calls $view->_add($sub_view);
4. executes $sub_view->renderHTML()

This returns a HTML that's stripped of any ID values, still linked to the main application but will not become part of the
render tree. This approach is useful when it's necessary to manipulate HTML and inject it directly into the template for
example when embedding UI elements into Grid Column.

Since Grid Column repeats the HTML many times, the ID values would be troublesome. Additionally, the render of a $sub_view
will be automatically embedded into the column and having it appear anywhere else on the page would be troublesome.

It's usually quite furtile to try and extract JS chains from the $sub_tree because JS wouldn't work anyways, so this method
will only work with static components.

.. toctree::
    :maxdepth: 4

    app
    init
    render
    template
    persistence
    callback
    virtualpage

